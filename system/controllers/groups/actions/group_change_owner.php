<?php

class actionGroupsGroupChangeOwner extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if (!$group['access']['is_owner'] && !$group['access']['is_moderator']){
            cmsCore::error404();
        }

        $form = $this->getForm('change_owner');

        $data = array();

        if ($this->request->has('email')){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this,  $data);

            $user = $this->model_users->getUserByEmail($data['email']);
            if((!$user || $user['email'] == $this->cms_user->email) && !$errors){
                $errors['email'] = ERR_USER_NOT_FOUND;
            }

            if(!$errors && cmsUser::getUPS('change_owner_'.$this->cms_user->id, $user['id'])){
                $errors['email'] = LANG_GROUPS_CHANGE_OWNER_SEND;
            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors,
                ));
            }

            $this->controller_messages->addRecipient($user['id']);

            $sender_link = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';
            $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

            $notice = array(
                'content' => sprintf(LANG_GROUPS_CHANGE_OWNER_NOTICE, $sender_link, $group_link),
                'options' => array(
                    'is_closeable' => false
                ),
                'actions' => array(
                    'accept' => array(
                        'title' => LANG_ACCEPT,
                        'controller' => $this->name,
                        'action'     => 'process_change_owner',
                        'params'     => array($group['id'], $this->cms_user->id, 'accept')
                    ),
                    'decline' => array(
                        'title'      => LANG_DECLINE,
                        'controller' => $this->name,
                        'action'     => 'process_change_owner',
                        'params'     => array($group['id'], $this->cms_user->id, 'decline')
                    )
                )
            );

            $this->controller_messages->sendNoticePM($notice);

            cmsUser::setUPS('change_owner_'.$this->cms_user->id, 1, $user['id']);

            return $this->cms_template->renderJSON(array(
                'errors'   => false,
                'success_text' => LANG_GROUPS_CHANGE_OWNER_SEND
            ));

        } else {

            return $this->cms_template->render('change_owner', array(
                'form_action' => href_to($this->name, $group['slug'], array('change_owner')),
                'data'   => $data,
                'form'   => $form,
                'errors' => (isset($errors) ? $errors : false),
                'group' => $group
            ));

        }

    }

}
