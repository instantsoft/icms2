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

    public function getForm($id) {

        $this->useCache('forms.form');

        if (is_numeric($id)) {

            $this->filterEqual('id', $id);
        } else {
            if (strlen($id) === 128) {
                $this->filterEqual('hash', $id);
            } else {
                $this->filterEqual('name', $id);
            }
        }

        $form = $this->getItem('forms', function ($item, $model) {

            $item['options'] = cmsModel::stringToArray($item['options']);

            return $item;
        });

        if (!$form) {
            return false;
        }

        $form['params'] = [
            'action'        => !empty($form['options']['action']) ? $form['options']['action'] : href_to('forms', 'send_ajax', $form['hash']),
            'hide_toolbar'  => true,
            'params'        => ['form_spam_token' => md5($form['hash'] . $form['name'])],
            'form_tpl_file' => $form['tpl_form'],
            'form_class'    => 'icms-forms__' . $form['name'],
            'submit'        => ['title' => !empty($form['options']['submit_title']) ? $form['options']['submit_title'] : LANG_SEND],
            'method'        => !empty($form['options']['method']) ? $form['options']['method'] : 'ajax'
        ];

        return $form;
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

                $field_property = $field;

                $field_class = 'field' . string_to_camel('_', $field['type']);

                $field['handler'] = new $field_class($field['name']);

                $field['handler_title'] = $field['handler']->getTitle();

                unset($field_property['type']);

                $field['handler']->setOptions($field_property);

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

    public function getFormFieldsets($form_id) {

        $name = $this->getTranslatedFieldName('fieldset', 'forms_fields');

        $this->useCache('forms.fields');

        $this->filterEqual('form_id', $form_id);

        $this->groupBy($name);

        if (!$this->order_by) {
            $this->orderBy($name);
        }

        $this->selectOnly($name, 'fieldset');

        return $this->get('forms_fields', function ($item, $model) {

            if (!$item['fieldset']) {
                return false;
            }
            return $item['fieldset'];
        }, false) ?: [];
    }

    public function addFormField($field){

        $this->filterEqual('form_id', $field['form_id']);

        $field['ordering'] = $this->getNextOrdering('forms_fields');

        // если создается новая группа, то выбираем ее
        if ($field['new_fieldset']) {
            $field[$this->getTranslatedFieldName('fieldset', 'forms_fields')] = $field['new_fieldset'];
        }

        $field['id'] = $this->insert('forms_fields', $field, true);

        cmsCache::getInstance()->clean('forms.fields');

        return $field['id'];
    }

    public function updateFormField($id, $field){

        // Если не выбрана группа, обнуляем поля групп
        foreach ($field as $key => $value) {
            if(strpos($key, 'fieldset') === 0 && !$value){
                $field[$key] = null;
            }
        }

        // если создается новая группа, то выбираем ее
        if ($field['new_fieldset']) {
            $field[$this->getTranslatedFieldName('fieldset', 'forms_fields')] = $field['new_fieldset'];
        }

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

    public function deleteController($id) {

        $this->db->dropTable('forms');
        $this->db->dropTable('forms_fields');

        cmsCache::getInstance()->clean('forms.form');
        cmsCache::getInstance()->clean('forms.fields');

        return parent::deleteController($id);
    }

}
