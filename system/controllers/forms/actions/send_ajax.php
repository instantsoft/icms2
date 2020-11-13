<?php

class actionFormsSendAjax extends cmsAction {

    private $attachment_fields = ['file', 'image', 'images'];

    public function run($hash){

        if(is_numeric($hash)){
            cmsCore::error404();
        }

        $_form_data = $this->model->getFormData($hash);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        $data = $form->parse($this->request, true);

        list($form, $form_data, $data) = cmsEventsManager::hook('forms_before_validate', [$form, $form_data, $data], null, $this->request);

        $errors = $form->validate($this, $data);

        list($form, $form_data, $data, $errors) = cmsEventsManager::hook('forms_after_validate', [$form, $form_data, $data, $errors], null, $this->request);

        if ($errors){
            return $this->cms_template->renderJSON(array(
                'errors' => $errors
            ));
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

                $name = $field->getName();

                // Данные по имени
                $form_items[$name] = $field->setItem($data)->getStringValue($data[$name]);
                // Данные по названию поля
                if($form_items[$name] !== null){
                    $form_items_titles[$field->title] = $form_items[$name];
                }
                // Вложения
                if(in_array($field->type, $this->attachment_fields) && $data[$name]){
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

        // Отправляем форму из стандартных опций
        foreach ($form_data['options']['send_type'] as $send_type) {

            $callback = 'send'.string_to_camel('_', $send_type);

            if(method_exists($this, $callback)){
                call_user_func_array([$this, $callback], [$form_data, $form_items, $form_items_titles, $attachments]);
            }
        }

        // В хуке можно отправлять форму еще куда-нибудь
        list($form, $form_data, $data, $form_items, $form_items_titles, $attachments) = cmsEventsManager::hook('forms_send_complete', [$form, $form_data, $data, $form_items, $form_items_titles, $attachments], null, $this->request);

        return $this->cms_template->renderJSON(array(
            'errors'        => false,
            'success_text'  => string_replace_keys_values_extended($send_text, $form_items),
            'continue_link' => (!empty($form_data['options']['continue_link']) ? $form_data['options']['continue_link'] : false),
            'callback'      => 'formsSuccess'
        ));

    }

    private function sendNotice($form_data, $form_items, $form_items_titles, $attachments) {

        if(empty($form_data['options']['send_type_notice'])){
            return false;
        }

        $emails = explode(',', trim($form_data['options']['send_type_notice'], ' ,'));
        $emails = array_map(function($val){ return trim($val); }, $emails);

        $this->model_users->filterIn('email', $emails);

        $recipients = $this->model_users->
                filterIsNull('is_locked')->
                filterIsNull('is_deleted')->
                limit(false)->getUsersIds();

        if (!$recipients) {
            return false;
        }

        // Делаем Ссылки на файл
        if($attachments){
            foreach ($attachments as $key => $path) {
                $form_items_titles[$key] = '<a href="'.$this->cms_config->upload_host_abs.'/'.$path.'" target="_blank">'.LANG_DOWNLOAD.'</a>';
            }
        }

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

        if(empty($form_data['options']['send_type_email'])){
            return false;
        }

        $emails = explode(',', trim($form_data['options']['send_type_email'], ' ,'));
        $emails = array_map(function($val){ return trim($val); }, $emails);

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

}
