<?php
class modelForms extends cmsModel {

    public function addForm($form){

        cmsCache::getInstance()->clean('forms.form');

        $form['hash'] = hash('sha512', string_random().serialize($form));
        return $this->insert('forms', $form, true);
    }

    public function updateForm($id, $form){

        cmsCache::getInstance()->clean('forms.form');

        return $this->update('forms', $id, $form, false, true);
    }

    public function deleteForm($id){

        cmsCache::getInstance()->clean('forms.form');
        cmsCache::getInstance()->clean('forms.fields');

        $this->filterEqual('form_id', $id);
        $this->deleteFiltered('forms_fields');

        return $this->delete('forms', $id);
    }

    public function getForm($id){

        $this->useCache('forms.form');

        if(is_numeric($id)){
            $this->filterEqual('id', $id);
        } else {
            if(strlen($id) === 128){
                $this->filterEqual('hash', $id);
            } else {
                $this->filterEqual('name', $id);
            }
        }

        $form = $this->getItem('forms');
        if(!$form){ return false; }

        $form['options'] = cmsModel::stringToArray($form['options']);

        $form['params'] = [
            'action'   => !empty($form['options']['action']) ? $form['options']['action'] : href_to('forms', 'send_ajax', $form['hash']),
            'hide_toolbar' => true,
            'tpl_form' => $form['tpl_form'],
            'submit'   => ['title' => LANG_SEND],
            'method'   => !empty($form['options']['method']) ? $form['options']['method'] : 'ajax'
        ];

        return $form;
    }

    public function getFormData($id){

        $form_data = $this->getForm($id);
        if(!$form_data){ return false;}

        $fields = $this->filterEqual('form_id', $form_data['id'])->getFormFields();
        if(!$fields){ return false; }

        // Строим форму
        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields);

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid);

            foreach($fieldset['fields'] as $field){
                // id, чтобы можно было на одной странице несколько форм выводить
                $field['handler']->id = $field['handler']->id.'_'.$form_data['id'];
                // Говорим, к чему это поле относится
                $field['handler']->context_params = [
                    'target_controller' => 'forms',
                    'target_subject'    => null,
                    'target_id'         => $form_data['id']
                ];
                $form->addField($fieldset_id, $field['handler']);
            }

        }

        list($form, $form_data) = cmsEventsManager::hook('forms_get_form', [$form, $form_data]);

        return [$form, $form_data];
    }


    public function getFormFields($enabled = true){

        $this->useCache('forms.fields');

        if($enabled){
            $this->filterEqual('is_enabled', 1);
        }

        $this->orderBy('ordering', 'asc');

        $fields = $this->get('forms_fields', function($item, $model) {

            $item['options'] = cmsModel::stringToArray($item['options']);
            $item['default'] = $item['values'];

            $rules = array();
            if (!empty($item['options']['is_required'])) {  $rules[] = array('required'); }
            if (!empty($item['options']['is_digits'])) {  $rules[] = array('digits'); }
            if (!empty($item['options']['is_number'])) {  $rules[] = array('number'); }
            if (!empty($item['options']['is_alphanumeric'])) {  $rules[] = array('alphanumeric'); }
            if (!empty($item['options']['is_email'])) {  $rules[] = array('email'); }
            if (!empty($item['options']['is_url'])) {  $rules[] = array('url'); }

            $item['rules'] = $rules;

            return $item;

        }, 'name');

        // чтобы сработала мультиязычность, если необходима
        // поэтому перебираем тут, а не выше
        if($fields){
            foreach ($fields as $name => $field) {

                $field_class = 'field' . string_to_camel('_', $field['type']);

                $field['handler'] = new $field_class($field['name']);

                $field['handler_title'] = $field['handler']->getTitle();

                $field['handler']->setOptions($field);

                $fields[$name] = $field;

            }
        }

        return $fields;
    }

    public function getFormField($id, $by_field = 'id'){

        $this->useCache('forms.fields');

        return $this->getItemByField('forms_fields', $by_field, $id, function($item, $model){

            $item['options'] = cmsModel::stringToArray($item['options']);

            $field_class = 'field' . string_to_camel('_', $item['type']);

            $handler = new $field_class($item['name']);

            $item['parser_title'] = $handler->getTitle();

            $handler->setOptions($item);

            $item['parser'] = $handler;

            return $item;

        });
    }

    public function getFormFieldsets($form_id){

        $this->useCache('forms.fields');

        $this->filterEqual('form_id', $form_id);

        $this->groupBy('fieldset');

        if (!$this->order_by){ $this->orderBy('fieldset'); }

        $fieldsets = $this->selectOnly('fieldset')->get('forms_fields', function($item, $model){
            if(!$item['fieldset']){ return false; }
            return $item['fieldset'];
        }, false);

        return (array)$fieldsets;
    }

    public function addFormField($field){

        $this->filterEqual('form_id', $field['form_id']);

        $field['ordering'] = $this->getNextOrdering('forms_fields');

        $field['id'] = $this->insert('forms_fields', $field, true);

        cmsCache::getInstance()->clean('forms.fields');

        return $field['id'];
    }

    public function updateFormField($id, $field){

        $result = $this->update('forms_fields', $id, $field, false, true);

        if ($result){
            $field['id'] = $id;
        }

        cmsCache::getInstance()->clean('forms.fields');

        return $result;
    }

    public function deleteFormField($id, $form_id){

        $this->delete('forms_fields', $id, 'id');

        $this->filterEqual('form_id', $form_id);

        $this->reorder('forms_fields');

        cmsCache::getInstance()->clean('forms.fields');

        return true;
    }

    public function reorderFormFields($fields_ids_list){

        $this->reorderByList('forms_fields', $fields_ids_list);

        cmsCache::getInstance()->clean('forms.fields');

        return true;
    }

    public function deleteController($id) {

        $this->db->dropTable('forms');
        $this->db->dropTable('forms_fields');

        cmsCache::getInstance()->clean('forms.form');
        cmsCache::getInstance()->clean('forms.fields');

        return parent::deleteController($id);
    }

}
