<?php

class actionAdminWidgetsRowAdd extends cmsAction {

    public function run($template_name){

        $tpls = cmsCore::getTemplates();

        if(!in_array($template_name, $tpls)){
            return cmsCore::error404();
        }

        $do = 'add';

        $form = $this->getForm('widgets_rows', [$do]);

        $row_scheme_options = cmsEventsManager::hookAll('admin_row_scheme_options_'.$template_name, [$do, [], []]);

        if($row_scheme_options){
            foreach ($row_scheme_options as $controller_name => $fields) {
                foreach ($fields as $field) {
                    $form->addField('basic', $field);
                }
            }
        }

        $is_submitted = $this->request->has('title');

        $row = array_merge($form->parse($this->request, $is_submitted), ['template' => $template_name]);

        $form->removeField('basic', 'nested_position');

        if ($is_submitted){

            $errors = $form->validate($this, $row);

            if (!$errors){

                $this->model_widgets->addLayoutRow($row);

                return $this->cms_template->renderJSON(array(
                    'errors' => false,
                    'redirect_uri' => href_to('admin', 'widgets').'?template_name='.$template_name
                ));

            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
            }

        }

        return $this->cms_template->render('widgets_rows', array(
            'action' => href_to('admin', 'widgets', ['row_add', $template_name]),
            'data'   => $row,
            'form'   => $form,
            'errors' => false
        ));

    }

}
