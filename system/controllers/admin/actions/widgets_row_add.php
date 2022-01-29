<?php

class actionAdminWidgetsRowAdd extends cmsAction {

    public function run($template_name){

        $tpls = cmsCore::getTemplates();

        if(!in_array($template_name, $tpls)){
            return cmsCore::error404();
        }

        $form = $this->getSchemeRowForm('add', ['template' => $template_name]);

        $is_submitted = $this->request->has('title');

        $row = array_merge($form->parse($this->request, $is_submitted), ['template' => $template_name]);

        $form->removeField('basic', 'nested_position');

        if ($is_submitted){

            $errors = $form->validate($this, $row);

            if (!$errors){

                // Для заполнения дефолтными настройками
                $default_col = $this->getSchemeColForm('add', $row)->parse(new cmsRequest([]), false);

                $row['id'] = $this->model_backend_widgets->addLayoutRow($row, $default_col);

                return $this->cms_template->renderJSON([
                    'errors' => false,
                    'redirect_uri' => href_to('admin', 'widgets').'?template_name='.$template_name.'&scroll_to=row-'.$row['id']
                ]);
            }

            if ($errors){
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render('widgets_rows', [
            'action' => href_to('admin', 'widgets', ['row_add', $template_name]),
            'data'   => $row,
            'form'   => $form,
            'errors' => false
        ]);
    }

}
