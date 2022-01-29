<?php

class actionAdminWidgetsRowAddNs extends cmsAction {

    public function run($col_id){

        $col = $this->model_backend_widgets->getLayoutCol($col_id);
        if (!$col) { cmsCore::error404(); }

        $row = $this->model_backend_widgets->getLayoutRow($col['row_id']);
        if (!$row) { cmsCore::error404(); }

        $row_data = ['template' => $row['template']];

        $form = $this->getSchemeRowForm('add_ns', $row_data, $col);

        if ($this->request->has('title')){

            $_row = $form->parse($this->request, true);

            $errors = $form->validate($this, $_row);

            if (!$errors){

                $_row['parent_id'] = $col['id'];

                // Для заполнения дефолтными настройками
                $default_col = $this->getSchemeColForm('add', $row)->parse(new cmsRequest([]), false);

                $_row['id'] = $this->model_backend_widgets->addLayoutRow($_row, $default_col);

                return $this->cms_template->renderJSON([
                    'errors' => false,
                    'redirect_uri' => href_to('admin', 'widgets').'?template_name='.$row['template'].'&scroll_to=row-'.$_row['id']
                ]);
            }

            if ($errors){
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render('widgets_rows', [
            'action' => href_to('admin', 'widgets', ['row_add_ns', $col['id']]),
            'data'   => $row_data,
            'form'   => $form,
            'errors' => false
        ]);
    }

}
