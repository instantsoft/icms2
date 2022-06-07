<?php

class actionGroupsGroupEdit extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $do = false){

        if(!$group['access']['is_can_edit']){
            cmsCore::error404();
        }

        // если нужно, передаем управление другому экшену
        if ($do){

            $this->current_params = array($group) + array_slice($this->params, 2);

            $this->runExternalAction('group_edit_'.$do, $this->current_params);

            return;

        }

        $form = $this->getGroupForm($group, 'edit');

        if (!$group['access']['is_owner'] && !$group['access']['is_moderator']){
            $form->removeFieldset('group_options');
        }

        if ($this->request->has('submit')){

            $group = array_merge($group, $form->parse($this->request, true, $group));

            $errors = $form->validate($this, $group);

            if (!$errors){

                $this->model->updateGroup($group['id'], $group);

                $this->model->fieldsAfterStore($group, $this->getGroupsFields(), 'edit');

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $group = $this->model->getGroup($group['id']);

                $content = cmsCore::getController('content', $this->request);

                $parents = $content->model->getContentTypeParents(null, $this->name);

                if($parents){
                    $content->bindItemToParents(array('id' => null, 'name' => $this->name, 'controller' => $this->name), $group, $parents);
                }

                $this->redirectToAction($group['slug']);

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        $page_title = LANG_GROUPS_EDIT;

        $this->cms_template->setPageTitle($page_title);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['id']));
        $this->cms_template->addBreadcrumb($page_title);

        return $this->cms_template->render('group_edit', array(
            'do'         => 'edit',
            'is_premoderation' => false,
            'page_title' => $page_title,
            'group'      => $group,
            'form'       => $form,
            'errors'     => isset($errors) ? $errors : false
        ));

    }

}
