<?php

class actionGroupsAdd extends cmsAction {

    public function run(){

        if (!cmsUser::isAllowed('groups', 'add')) { cmsCore::error404(); }

        $form = $this->getGroupForm();

        $fields = $this->getGroupsFields();

        $is_submitted = $this->request->has('submit');

        $group = $form->parse($this->request, $is_submitted);

        $group['ctype_name'] = $this->name;

        // Заполняем поля значениями по умолчанию, взятыми из профиля пользователя
        // (для тех полей, в которых это включено)
        foreach($fields as $field){
            if (!empty($field['options']['profile_value'])){
                $group[$field['name']] = $this->cms_user->{$field['options']['profile_value']};
            }
        }

        $is_premoderation = cmsUser::isAllowed('groups', 'add', 'premod') && !$this->cms_user->is_admin;

        if ($is_submitted){

            $errors = $form->validate($this, $group);

            if (!$errors){

                $group['owner_id'] = $this->cms_user->id;

                $group['is_approved'] = !$is_premoderation;

                $id = $this->model->addGroup($group);

                $group = $this->model->getGroup($id);

                $this->model->fieldsAfterStore($group, $fields);

                $parents = $this->controller_content->model->getContentTypeParents(null, $this->name);

                if($parents){
                    $this->controller_content->bindItemToParents(array(
                        'id'         => null,
                        'name'       => $this->name,
                        'controller' => $this->name
                    ), $group, $parents);
                }

                if (!$group['is_approved']){

                    $group['page_url'] = href_to_abs('groups', $group['slug']);

                    $group['url'] = href_to_rel('groups', $group['slug']);

                    $succes_text = cmsCore::getController('moderation')->requestModeration('groups', $group);

                    if($succes_text){
                        cmsUser::addSessionMessage($succes_text, 'info');
                    }

                } else {
                    cmsEventsManager::hook('content_groups_after_add_approve', $group);
                }

                $this->redirectToAction($group['slug']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $page_title = LANG_GROUPS_ADD;

        $this->cms_template->setPageTitle($page_title);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to($this->name));
        $this->cms_template->addBreadcrumb($page_title);

        return $this->cms_template->render('group_edit', array(
            'is_premoderation' => $is_premoderation,
            'do'         => 'add',
            'page_title' => $page_title,
            'group'      => $group,
            'form'       => $form,
            'errors'     => isset($errors) ? $errors : false
        ));

    }

}
