<?php

class actionAdminWidgetsColAdd extends cmsAction {

    public function run($row_id){

        $row = $this->model_widgets->getLayoutRow($row_id);
        if (!$row) { cmsCore::error404(); }

        $form = $this->getForm('widgets_cols', ['add', 0]);

        $col_scheme_options = cmsEventsManager::hookAll('admin_col_scheme_options_'.$row['template'], ['add', $row, []]);

        if($col_scheme_options){
            foreach ($col_scheme_options as $controller_name => $fields) {
                foreach ($fields as $field) {
                    $form->addField('basic', $field);
                }
            }
        }

        if ($this->request->has('title')){

            $col = $form->parse($this->request, true);

            $errors = $form->validate($this, $col);

            if (!$errors){

                $this->model_widgets->addLayoutCol($col);

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
            'action' => href_to('admin', 'widgets', ['col_add', $row['id']]),
            'data'   => ['row_id' => $row['id']],
            'form'   => $form,
            'errors' => false
        ));

    }

}
