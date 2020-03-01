<?php

class actionAdminWidgetsRowAddNs extends cmsAction {

    public function run($col_id){

        $do = 'add_ns';

        $col = $this->model_widgets->getLayoutCol($col_id);
        if (!$col) { cmsCore::error404(); }

        $row = $this->model_widgets->getLayoutRow($col['row_id']);
        if (!$row) { cmsCore::error404(); }

        $row_data = ['template' => $row['template']];

        $form = $this->getForm('widgets_rows', [$do]);

        $row_scheme_options = cmsEventsManager::hookAll('admin_row_scheme_options_'.$row['template'], [$do, $row, $col]);

        if($row_scheme_options){
            foreach ($row_scheme_options as $controller_name => $fields) {
                foreach ($fields as $field) {
                    $form->addField('basic', $field);
                }
            }
        }

        if ($this->request->has('title')){

            $_row = $form->parse($this->request, true);

            $errors = $form->validate($this, $_row);

            if (!$errors){

                $_row['parent_id'] = $col['id'];

                $this->model_widgets->addLayoutRow($_row);

                return $this->cms_template->renderJSON(array(
                    'errors' => false,
                    'redirect_uri' => href_to('admin', 'widgets').'?template_name='.$row['template']
                ));

            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
            }

        }

        return $this->cms_template->render('widgets_rows', array(
            'action' => href_to('admin', 'widgets', ['row_add_ns', $col['id']]),
            'data'   => $row_data,
            'form'   => $form,
            'errors' => false
        ));

    }

}
