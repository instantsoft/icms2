<?php

class actionFormsSendAjax extends cmsAction {

    private $attachment_fields = ['file', 'image', 'images'];

    public $request_params = [
        'form_name' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'form_spam_token' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname'],
                ['max_length', 32]
            ]
        ]
    ];

    public function run($hash){

        if (!$this->request->isAjax()) { return cmsCore::error404(); }

        if(is_numeric($hash)){
            cmsCore::error404();
        }

        // Ячейка, в которой все данные формы
        $form_fields_group_name = $this->request->get('form_name');

        $_form_data = $this->getFormData($hash, $form_fields_group_name);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        // Проверяем спам токен, который отправляется дополнительно к параметрам формы
        $form_spam_token = md5($form_data['hash'].$form_data['name']);
        $form_spam_token_post = $this->request->get('form_spam_token');
        if($form_spam_token !== $form_spam_token_post){
            return $this->cms_template->renderJSON([
                'errors' => ['form_spam_token' => 'not equal']
            ]);
        }

        // Данные формы текущего юзера, если отправляли ранее
        $submited_data = $this->getSavedUserFormData($form_data['id']);
        // Если форма скрывается после отправки, не даём еще раз отправить
        if($submited_data){
            if(!empty($form_data['options']['hide_after_submit'])){
                return $this->cms_template->renderJSON([
                    'message' => LANG_FORMS_FORM_IS_SENDED,
                    'errors' => []
                ]);
            }
        }

        $data = $form->parse($this->request, true);

        list($form, $form_data, $data) = cmsEventsManager::hook('forms_before_validate', [$form, $form_data, $data], null, $this->request);

        $errors = $form->validate($this, $data);

        list($form, $form_data, $data, $errors) = cmsEventsManager::hook('forms_after_validate', [$form, $form_data, $data, $errors], null, $this->request);

        if ($errors){
            return $this->cms_template->renderJSON([
                'errors' => $errors
            ]);
        }

        // Делаем массив данных формы нормальными, без $form_fields_group_name
        $data = $data[$form_fields_group_name];

        // Фейковое поле не должно быть заполнено
        if(!empty($data['fake_string'])){
            return $this->cms_template->renderJSON([
                'errors' => ['fake_string' => 'not empty']
            ]);
        }

        $send_text = $this->options['send_text'];
        if(!empty($form_data['options']['send_text'])){
            $send_text = $form_data['options']['send_text'];
        }

        $form_items = []; $form_items_titles = []; $attachments = [];

        // Формируем массив данных формы
        foreach ($form->getStructure() as $fieldset) {

            if (empty($fieldset['childs'])) { continue; }

            foreach($fieldset['childs'] as $field){

                $name = str_replace($form_fields_group_name.':', '', $field->getName());

                if(!array_key_exists($name, $data)){
                    continue;
                }

                $field->setName($name);

                // Данные по имени
                $form_items[$name] = $field->setItem($data)->getStringValue($data[$name]);
                // Данные по названию поля
                if($form_items[$name] !== null){
                    $form_items_titles[$field->title] = $form_items[$name];
                }
                // Вложения
                if(in_array($field->field_type, $this->attachment_fields) && $data[$name]){
                    $files = $field->getFiles($data[$name]);
                    if($files){
                        $is_number = count($files) > 1;
                        foreach ($files as $path_key => $path) {
                            $attachments[$field->title.($is_number ? $path_key+1 : '')] = $path;
                        }
                    }
                }
            }
        }

        // Для доступности выражения названия формы
        $form_items['form_title'] = $form_data['title'];
        // Сформированный HTML всех данных формы
        $form_items['form_data'] = $this->cms_template->getRenderedChild('form_data', [
            'form_items_titles' => $form_items_titles
        ]);
        // IP адрес
        $form_items['ip'] = cmsUser::getIp();
        $form_items['user_name'] = $this->cms_user->is_logged ? $this->cms_user->nickname : LANG_GUEST;

        // Отправляем форму из стандартных опций
        foreach ($form_data['options']['send_type'] as $send_type) {

            $callback = 'send'.string_to_camel('_', $send_type);

            if(method_exists($this, $callback)){
                call_user_func_array([$this, $callback], [$form_data, $form_items, $form_items_titles, $attachments]);
            }
        }

        // В хуке можно отправлять форму еще куда-нибудь
        list($form, $form_data, $data, $form_items, $form_items_titles, $attachments) = cmsEventsManager::hook('forms_send_complete', [$form, $form_data, $data, $form_items, $form_items_titles, $attachments], null, $this->request);

        // Фиксируем, что форма отправлена
        $this->saveUserFormData([
            'form_data'         => $form_data,
            'data'              => $data,
            'form_items'        => $form_items,
            'form_items_titles' => $form_items_titles,
            'attachments'       => $attachments
        ]);

        $success_html = $this->cms_template->renderInternal($this, 'form_success', [
            'form_data' => $form_data,
            'success_text'  => string_replace_keys_values_extended($send_text, $form_items),
            'hide_after_submit' => !empty($form_data['options']['hide_after_submit'])
        ]);

        return $this->cms_template->renderJSON([
            'errors'       => false,
            'success_html' => $success_html,
            'form_id'      => $form_fields_group_name,
            'callback'     => 'formsSuccess'
        ]);
    }

    private function sendNotice($form_data, $form_items, $form_items_titles, $attachments) {

        $emails = []; $recipients = [];

        if(!empty($form_data['options']['send_type_notice'])){

            $emails = explode(',', trim($form_data['options']['send_type_notice'], ' ,'));
            $emails = array_map(function($val){ return trim($val); }, $emails);

            $this->model_users->filterIn('email', $emails);

            $recipients = $this->model_users->
                    filterIsNull('is_locked')->
                    filterIsNull('is_deleted')->
                    limit(false)->getUsersIds() ?: [];

        }

        // Если стоит отправка автору записи
        if(in_array('author', $form_data['options']['send_type'])){

            $author_id = $this->request->get('author_id', 0);

            if($author_id){
                $recipients[] = $author_id;

                $recipients = array_unique($recipients);
            }
        }

        if (!$recipients) {
            return false;
        }

        // Делаем Ссылки на файл
        if($attachments){
            foreach ($attachments as $key => $path) {
                $form_items_titles[$key] = '<a href="'.$this->cms_config->upload_host_abs.'/'.$path.'" target="_blank">'.LANG_DOWNLOAD.'</a>';
            }
        }

        // Перерендерим с учётом ссылок
        $form_items['form_data'] = $this->cms_template->getRenderedChild('form_data', [
            'form_items_titles' => $form_items_titles
        ]);

        $this->controller_messages->clearRecipients();

        foreach ($recipients as $user_id) {
            $this->controller_messages->addRecipient($user_id);
        }

        $notify_text = !empty($form_data['options']['notify_text']) ? $form_data['options']['notify_text'] : $this->options['notify_text'];

        $this->controller_messages->sendNoticePM(array(
            'content' => string_replace_keys_values_extended($notify_text, $form_items)
        ));

        return true;
    }

    private function sendEmail($form_data, $form_items, $form_items_titles, $attachments) {

        $emails = [];

        if(!empty($form_data['options']['send_type_email'])){
            $emails = explode(',', trim($form_data['options']['send_type_email'], ' ,'));
            $emails = array_map(function($val){ return trim($val); }, $emails);
        }

        // Если стоит отправка автору записи
        if(in_array('author', $form_data['options']['send_type'])){
            $author_id = $this->request->get('author_id', 0);
            if($author_id){
                $author = $this->model_users->getUser($author_id);
                if($author){
                    $emails[] = $author['email'];
                    $emails = array_unique($emails);
                }
            }
        }

        if(!$emails){
            return false;
        }

        $letter = !empty($form_data['options']['letter']) ? $form_data['options']['letter'] : $this->options['letter'];

        // Делаем полный путь к файлу
        if($attachments){
            foreach ($attachments as $key => $path) {
                $attachments[$key] = $this->cms_config->upload_path.$path;
            }
        }

        foreach ($emails as $email) {
            $this->controller_messages->sendEmail(['email' => $email, 'attachments' => $attachments], [
                'text' => $letter
            ], $form_items, false);
        }

        return true;
    }

    private function saveUserFormData($data) {

        $form_id = $data['form_data']['id'];

        // Для гостей просто сессия
        if(!$this->cms_user->is_logged){
            cmsUser::sessionSet('forms:'.$form_id, ['ip' => cmsUser::getIp()]);
            return true;
        }

        $data['ip'] = cmsUser::getIp();

        unset($data['form_data']);

        cmsUser::setUPS('forms.data.'.$form_id, $data);

        return true;
    }

}
