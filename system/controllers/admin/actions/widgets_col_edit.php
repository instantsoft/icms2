<?php

class actionAdminWidgetsColEdit extends cmsAction {

    public function run($id){

        $col = $this->model_backend_widgets->getLayoutCol($id);
        if (!$col) { cmsCore::error404(); }

        $row = $this->model_backend_widgets->getLayoutRow($col['row_id']);
        if (!$row) { cmsCore::error404(); }

        $form = $this->getSchemeColForm('edit', $row, $col);

        if ($this->request->has('title')){

            $_col = $form->parse($this->request, true);

            $errors = $form->validate($this, $_col);

            if (!$errors){

                $this->model_backend_widgets->updateLayoutCol($col['id'], $_col);

                // Если изменилось название позиции, меняем в виджетах
                if($col['name'] !== $_col['name']){
                    $this->model_backend_widgets->updateWidgetBindPosition($col['name'], $_col['name'], $row['template']);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                return $this->cms_template->renderJSON([
                    'errors' => false,
                    'redirect_uri' => href_to('admin', 'widgets').'?template_name='.$row['template'].'&scroll_to=col-'.$col['id']
                ]);
            }

            if ($errors){
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render('widgets_rows', [
            'action' => href_to('admin', 'widgets', ['col_edit', $col['id']]),
            'data'   => $col,
            'form'   => $form,
            'errors' => false
        ]);
    }

}
