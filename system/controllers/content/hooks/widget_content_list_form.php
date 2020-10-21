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

                $form->addField('fields_options',
                    new cmsFormField('fake', array(
                            'title' => '',
                            'hint' => LANG_WD_CONTENT_LIST_FIELDS_HINT,
                            'html' => ''
                        )
                    )
                );

                foreach ($fields as $field) {

                    if ($field['is_system']) { continue; }

                    $name = "options:show_fields:{$ctype['id']}:{$field['name']}";

                    $form->addField('fields_options',
                        new fieldCheckbox($name, array(
                                'title' => $field['title']
                            )
                        )
                    );

                    $options = $field['handler']->getOptionsExtended();

                    if($options){
                        foreach ($options as $option_field) {

                            $option_field->setName('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':'.$option_field->getName());
                            $option_field->setProperty('visible_depend', [$name => array('show' => array('1'))]);

                            $form->addField('fields_options', $option_field);
                        }
                    }
                }

                $form->addField('fields_options',
                    new fieldCheckbox("options:show_fields:{$ctype['id']}:date_pub", array(
                            'title' => LANG_DATE
                        )
                    )
                );
                $form->addField('fields_options',
                    new fieldCheckbox("options:show_fields:{$ctype['id']}:user", array(
                            'title' => LANG_AUTHOR
                        )
                    )
                );
                $form->addField('fields_options',
                    new fieldCheckbox("options:show_fields:{$ctype['id']}:comments", array(
                            'title' => LANG_COMMENTS
                        )
                    )
                );

            }

        }

        return [$form, $widget, $widget_object, $template];
    }

}
