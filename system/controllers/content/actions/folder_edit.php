<?php
/**
 * @property \modelContent $model
 */
class actionContentFolderEdit extends cmsAction {

    public function run() {

        $id = $this->request->get('id', 0);
        if (!$id) { return cmsCore::error404(); }

        $folder = $this->model->localizedOff()->getContentFolder($id);
        if (!$folder) { return cmsCore::error404(); }

        $this->model->localizedRestore();

        if (($folder['user_id'] != $this->cms_user->id) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $ctype = $this->model->getContentType($folder['ctype_id']);

        $form = $this->getForm('folder');

        // Форма отправлена?
        if ($this->request->has('submit')) {

            // Парсим форму и получаем поля записи
            $updated_folder = $form->parse($this->request, true);

            // Проверям правильность заполнения
            $errors = $form->validate($this, $updated_folder);

            if (!$errors) {

                // Обновляем папку и редиректим на ее просмотр
                $this->model->updateContentFolder($id, $updated_folder);

                $this->redirect(href_to_profile($folder['user'], ['content', $ctype['name'], $folder['id']]));
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('folder_form', [
            'ctype'  => $ctype,
            'folder' => $folder,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
