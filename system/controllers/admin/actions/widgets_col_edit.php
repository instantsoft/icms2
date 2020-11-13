<?php

class actionAdminWidgetsColEdit extends cmsAction {

    public function run($id){

        $col = $this->model_widgets->getLayoutCol($id);
        if (!$col) { cmsCore::error404(); }

        $row = $this->model_widgets->getLayoutRow($col['row_id']);
        if (!$row) { cmsCore::error404(); }

        $form = $this->getSchemeColForm('edit', $row, $col);

        if ($this->request->has('title')){

            $_col = $form->parse($this->request, true);

            $errors = $form->validate($this, $_col);

            if (!$errors){

                $this->model_widgets->updateLayoutCol($col['id'], $_col);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

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
            'action' => href_to('admin', 'widgets', ['col_edit', $col['id']]),
            'data'   => $col,
            'form'   => $form,
            'errors' => false
        ));

    }

}
