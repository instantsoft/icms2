<?php

class actionGroupsInviteUsers extends cmsAction {

    public function run($group_id){

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_can_invite_users']){
            cmsCore::error404();
        }

        $form = $this->getForm('invite_users', array($group));

        $data = array();

        if ($this->request->has('is_submit')){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this,  $data);

            // с required в форме не работало, надо разобраться
            if(!$data['users_list']){
                $errors['users_list'] = ERR_VALIDATE_REQUIRED;
            }

            if (!$errors){

                $invited_list = array();

                foreach($data['users_list'] as $user_id){
                    if (!$this->model->getInvite($group_id, $user_id)){
                        $invited_list[] = $user_id;
                    }
                }

                return $this->sendInvite($invited_list, $group_id);

            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
            }

        }

        return $this->cms_template->render('invite_users', array(
            'data'   => $data,
            'form'   => $form,
            'errors' => (isset($errors) ? $errors : false),
            'group'  => $group
        ));

    }

}
