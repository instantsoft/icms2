<?php

class actionContentWidgetFieldsOptionsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax() || !cmsUser::isAdmin()) {
            return cmsCore::error404();
        }

        $ctype_id = $this->request->get('value', 0);
        if(!$ctype_id){ return $this->halt(); }

        $form_id = $this->request->get('form_id', '');

		$ctype = $this->model->getContentType($ctype_id);
		if (!$ctype) { return $this->halt(); }

        cmsCore::loadWidgetLanguage('list', 'content');

		$fields = $this->model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $form = new cmsForm();

        $fset_id = $form->addFieldset();

        $form->addField($fset_id,
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

            $form->addField($fset_id,
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

                    $form->addField($fset_id, $option_field);
                }
            }
        }

        $form->addField($fset_id,
            new fieldCheckbox("options:show_fields:{$ctype['id']}:date_pub", array(
                    'title' => LANG_DATE
                )
            )
        );
        $form->addField($fset_id,
            new fieldCheckbox("options:show_fields:{$ctype['id']}:user", array(
                    'title' => LANG_AUTHOR
                )
            )
        );
        $form->addField($fset_id,
            new fieldCheckbox("options:show_fields:{$ctype['id']}:comments", array(
                    'title' => LANG_COMMENTS
                )
            )
        );

        ob_start();

        $this->cms_template->renderForm($form, [], [
            'only_fields' => true,
            'form_id' => $form_id,
            'form_tpl_file' => 'form_fields'
        ]);

        return die(ob_get_clean());
    }

}
