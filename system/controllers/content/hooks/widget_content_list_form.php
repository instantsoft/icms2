<?php

class onContentWidgetContentListForm extends cmsAction {

    public function run($data){

        list($form, $widget, $widget_object, $template) = $data;

        $ctype_id = empty($widget['options']['ctype_id']) ? 0 : intval($widget['options']['ctype_id']);

        if($this->request->hasInArray('options:ctype_id')){
            $ctype_id = $this->request->get('options:ctype_id', 0);
        }

        if($ctype_id){

            $ctype = $this->model->getContentType($ctype_id);

            if ($ctype) {

                $fields = $this->model->getContentFields($ctype['name']);

                $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

                $form->clearFieldset('fields_options');

                $form_fields = $this->getForm('widget_content_list', [$ctype, $fields]);

                foreach($form_fields->getStructure() as $fieldset_id => $fieldset){
                    foreach($fieldset['childs'] as $field){
                        $form->addField('fields_options', $field);
                    }
                }

            }

        }

        return [$form, $widget, $widget_object, $template];
    }

}
