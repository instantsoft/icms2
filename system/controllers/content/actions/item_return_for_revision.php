<?php

class actionContentItemReturnForRevision extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) { cmsCore::error404(); }

        if ($item['is_approved'] || $item['is_draft']){ cmsCore::error404(); }

        // Проверяем права
        $is_moderator = $this->cms_user->is_admin || $this->controller_moderation->model->userIsContentModerator($ctype['name'], $this->cms_user->id);
        if (!$is_moderator){ cmsCore::error404(); }

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        $form->addField($fieldset_id, new fieldText('remarks', array(
            'title' => LANG_MODERATION_REMARKS,
            'rules' => array(
                array('required')
            )
        )));

        $form->addField($fieldset_id, new fieldHidden('submit'));

        if(!$this->request->has('submit')){

            return $this->cms_template->render('revision_form', array(
                'form'=> $form,
                'form_action'=> href_to($ctype['name'], 'return_for_revision', $item['id'])
            ));

        }

        $data = $form->parse($this->request, true);

        $errors = $form->validate($this,  $data);

        if (!$errors){

            $item['reason'] = $data['remarks'];
            $item['page_url'] = href_to_abs($ctype['name'], 'edit', $item['id']);

            $this->controller_moderation->reworkModeratorTask($ctype['name'], $item, $this->getUniqueKey(array($ctype['name'], 'moderation', $item['id'])));

            ob_start();

            cmsUser::addSessionMessage(LANG_MODERATION_REMARK_NOTIFY, 'success');

            $this->cms_template->renderAsset('ui/redirect_continue', array(
                'redirect_url' => href_to('moderation')
            ));

            return $this->cms_template->renderJSON(array(
                'errors'       => false,
                'success_text' => ob_get_clean()
            ));

        }

        if ($errors){
            return $this->cms_template->renderJSON(array(
                'errors' => $errors
            ));
        }

    }

}
