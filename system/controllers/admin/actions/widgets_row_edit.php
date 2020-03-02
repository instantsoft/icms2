<?php

class actionAdminWidgetsRowEdit extends cmsAction {

    public function run($id){

        $row = $this->model_widgets->getLayoutRow($id);
        if (!$row) { cmsCore::error404(); }

        $form = $this->getSchemeRowForm('edit', $row);

        $form->removeField('basic', 'cols_count');

        if(!$row['parent_id']){
            $form->removeField('basic', 'nested_position');
        }

        if ($this->request->has('title')){

            $_row = $form->parse($this->request, true);

            $errors = $form->validate($this, $_row);

            if (!$errors){

                $this->model_widgets->updateLayoutRow($row['id'], $_row);

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
            'action' => href_to('admin', 'widgets', ['row_edit', $row['id']]),
            'data'   => $row,
            'form'   => $form,
            'errors' => false
        ));

    }

}
