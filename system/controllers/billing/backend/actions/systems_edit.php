<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingSystemsEdit extends cmsAction {

    public function run($id) {

        if (!$id) {
            return cmsCore::error404();
        }

        $system = $this->model->getPaymentSystem($id);

        if (!$system) {
            return cmsCore::error404();
        }

        $form = $this->getForm('system', [$this->options]);

        $system_form = $this->getPaymentSystemOptionsForm($system['name']);

        if ($system_form) {

            $system_form_struct = $system_form->getStructure();

            $form->addFieldset(LANG_OPTIONS, 'options');

            foreach ($system_form_struct['options']['childs'] as $field) {
                $form->addField('options', $field);
            }
        }

        if ($this->request->has('submit')) {

            $system = $form->parse($this->request, true);
            $errors = $form->validate($this, $system);

            if (!$errors) {

                $this->model->updatePaymentSystem($id, $system);

                return $this->redirectToAction('systems');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/system', [
            'form'   => $form,
            'system' => $system,
            'errors' => $errors ?? false
        ]);
    }

}
