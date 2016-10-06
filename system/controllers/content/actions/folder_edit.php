<?php

class actionContentFolderEdit extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $folder = $this->model->getContentFolder($id);

        if (!$folder) { cmsCore::error404(); }

        if (($folder['user_id'] != $user->id) && !$user->is_admin){
            cmsCore::error404();
        }

        $ctype = $this->model->getContentType($folder['ctype_id']);

        $form = $this->getForm('folder');

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $updated_folder = $form->parse($this->request, $is_submitted);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $updated_folder);

            if (!$errors){

                // Обновляем папку и редиректим на ее просмотр
                $this->model->updateContentFolder($id, $updated_folder);

                $this->redirect( href_to('users', $folder['user_id'], array('content', $ctype['name'], $folder['id'])) );

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('folder_form', array(
            'ctype' => $ctype,
            'folder' => $folder,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
