<?php

class actionContentItemReturnForRevision extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) {
            return cmsCore::error404();
        }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) {
            return cmsCore::error404();
        }

        if ($item['is_approved'] || $item['is_draft']) {
            return cmsCore::error404();
        }

        // Проверяем права
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);
        if (!$is_moderator) {
            return cmsCore::error404();
        }

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        $form->addField($fieldset_id, new fieldText('remarks', [
            'title' => LANG_MODERATION_REMARKS,
            'rules' => [
                ['required']
            ]
        ]));

        $form->addField($fieldset_id, new fieldHidden('submit'));

        if (!$this->request->has('submit')) {

            return $this->cms_template->render('revision_form', [
                'form'        => $form,
                'form_action' => href_to($ctype['name'], 'return_for_revision', $item['id'])
            ]);
        }

        $data = $form->parse($this->request, true);

        $errors = $form->validate($this, $data);

        if (!$errors) {

            $item['reason']   = $data['remarks'];
            $item['page_url'] = href_to_abs($ctype['name'], 'edit', $item['id']);

            $this->controller_moderation->reworkModeratorTask($ctype['name'], $item, $this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]));

            ob_start();

            cmsUser::addSessionMessage(LANG_MODERATION_REMARK_NOTIFY, 'success');

            $this->cms_template->renderAsset('ui/redirect_continue', [
                'redirect_url' => href_to('moderation')
            ]);

            return $this->cms_template->renderJSON([
                'errors'       => false,
                'success_text' => ob_get_clean()
            ]);
        }

        return $this->cms_template->renderJSON([
            'errors' => $errors
        ]);
    }

}
