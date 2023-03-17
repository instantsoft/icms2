<?php
/**
 * @property \modelForms $model
 */
class forms extends cmsFrontend {

    protected $useOptions = true;

    protected $unknown_action_as_index_param = true;

    public function getOptions(){

        $options = (array)self::loadOptions($this->name);

        if(!empty($options['allow_embed_domain'])){
            $allow_embed_domain_array = explode(',', $options['allow_embed_domain']);
            $options['allow_embed_domain_array'] = array_map(function($val){ return trim($val); }, $allow_embed_domain_array);
        }
        if(!empty($options['denied_embed_domain'])){
            $allow_embed_domain_array = explode(',', $options['denied_embed_domain']);
            $options['denied_embed_domain_array'] = array_map(function($val){ return trim($val); }, $allow_embed_domain_array);
        }

        return $options;
    }

    public function isAllowEmbed() {

        if(empty($this->options['allow_embed'])){
            return false;
        }

        $is_external    = true;
        $show_on_domain = true;

        if(isset($_SERVER['HTTP_REFERER'])){

            $refer      = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            $is_external = (($refer == $_SERVER['HTTP_HOST']) ? false : true);

            if(!empty($this->options['allow_embed_domain_array']) && $is_external){
                $show_on_domain = ($refer && in_array($refer, $this->options['allow_embed_domain_array']));
            }

            if(!empty($this->options['denied_embed_domain_array']) && $is_external){
                $show_on_domain = !($refer && in_array($refer, $this->options['denied_embed_domain_array']));
            }

        }

        return $show_on_domain;
    }

    public function parseShortcode($string, $item = []) {

        $matches_count = preg_match_all('/{forms:([a-z0-9_]+)}/i', $string, $matches);

        if ($matches_count) {
            for ($i = 0; $i < $matches_count; $i++) {

                $tag  = $matches[0][$i];
                $name = $matches[1][$i];

                $_form_data = $this->getFormData($name);

                if ($_form_data === false) {
                    continue;
                }

                list($form, $form_data) = $_form_data;

                if(!empty($item['user_id'])){
                    $form = $this->setItemAuthor($form, $item['user_id']);
                }

                $submited_data = $this->getSavedUserFormData($form_data['id']);

                if($submited_data && !empty($form_data['options']['hide_after_submit'])){
                    $string = str_replace($tag, '', $string);
                    continue;
                }

                $form_html = $this->cms_template->renderInternal($this, 'form_view', [
                    'submited_data' => $submited_data,
                    'form_data' => $form_data,
                    'form'      => $form
                ]);

                $string = str_replace($tag, $form_html, $string);
            }
        }

        return $string;
    }

    public function getFormData($id, $submit_form_name = false){

        $form_data = $this->model->getForm($id);
        if(!$form_data){ return false;}

        $fields = $this->model->filterEqual('form_id', $form_data['id'])->getFormFields();
        if(!$fields){ return false; }

        // Строим форму
        $form = new cmsForm();

        if(!$submit_form_name){
            $submit_form_name = string_random();
        }

        // Вспомогательные поля
        $fieldset_id = $form->addFieldset('', 'system', ['is_hidden' => true]);
        // Для того, чтобы выводить одну и ту же форму несколько раз на странице
        $form->addField($fieldset_id, new fieldHidden('form_name', [
            'default' => $submit_form_name,
            'show_id_attr' => false
        ]));
        $form->addField($fieldset_id, new fieldHidden('author_id', [
            'default' => 0,
            'show_id_attr' => false
        ]));
        $form->addField($fieldset_id, new fieldHidden('context_target', [
            'default' => '',
            'show_id_attr' => false
        ]));
        $form->addField($fieldset_id, new fieldHidden($submit_form_name.':page_url', [
            'title' => LANG_FORMS_PAGE_URL,
            'default' => $this->cms_config->host . $this->cms_core->uri_absolute
        ]));
        $form->addField($fieldset_id, new fieldHidden($submit_form_name.':fake_string'));

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields);

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid);

            foreach($fieldset['fields'] as $field){
                $field['handler']->setName($submit_form_name.':'.$field['handler']->getName());
                // Говорим, к чему это поле относится
                $field['handler']->context_params = [
                    'target_controller' => 'forms',
                    'target_subject'    => null,
                    'target_id'         => $form_data['id']
                ];
                $form->addField($fieldset_id, $field['handler']);
            }

        }

        // Запоминаем идентификатор формы
        $form_data['params']['form_id'] = $submit_form_name;

        list($form, $form_data) = cmsEventsManager::hook('forms_get_form', [$form, $form_data]);

        return [$form, $form_data];
    }

    public function setContextTarget($form, $target) {

        $form->setFieldProperty('system', 'context_target', 'default', $target);

        return $form;
    }

    public function setItemAuthor($form, $user_id) {

        $form->setFieldProperty('system', 'author_id', 'default', $user_id);

        return $form;
    }

    public function getSavedUserFormData($form_id) {
        if(!$this->cms_user->is_logged){
            return cmsUser::sessionGet('forms:'.$form_id);
        } else {
            return cmsUser::getUPS('forms.data.'.$form_id);
        }
    }

}
