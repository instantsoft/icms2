<?php

class actionAdminWidgetsColAdd extends cmsAction {

    public function run($row_id){

        $row = $this->model_backend_widgets->getLayoutRow($row_id);
        if (!$row) { cmsCore::error404(); }

        $form = $this->getSchemeColForm('add', $row);

        if ($this->request->has('title')){

            $col = $form->parse($this->request, true);

            $errors = $form->validate($this, $col);

            if (!$errors){

                $this->model_backend_widgets->addLayoutCol($col);

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
