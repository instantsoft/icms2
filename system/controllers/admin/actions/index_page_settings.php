<?php

class actionAdminIndexPageSettings extends cmsAction {

    public function run() {

        $dashboard_blocks = cmsEventsManager::hookAll('admin_dashboard_block', ['only_titles' => true], []);

        $result_blocks = [];

        foreach ($dashboard_blocks as $dashboard_block) {
            foreach ($dashboard_block as $key => $item) {
                $result_blocks[$key] = $item;
            }
        }

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        foreach ($result_blocks as $name => $title) {

            $form->addField($fieldset_id,
                new fieldCheckbox('dashboard_enabled:' . $name, [
                    'title'   => $title,
                    'default' => 1
                ])
            );
        }

        if (!$this->request->isAjax()) {

            if (!$this->request->has('submit')) {
                return cmsCore::error404();
            }

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors) {
                cmsController::saveOptions('admin', array_merge($this->options, $data));
            }

            if ($errors) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            return $this->redirectToAction('index');
        }

        $values = !empty($this->options) ? $this->options : [];

        return $this->cms_template->render('index_page_settings', [
            'values' => $values,
            'errors' => isset($errors) ? $errors : false,
            'form'   => $form
        ]);
    }

}
