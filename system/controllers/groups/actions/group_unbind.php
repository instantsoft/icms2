<?php

class actionGroupsGroupUnbind extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $ctype_name, $item_id) {

        if ($group['access']['member_role'] != groups::ROLE_STAFF) {
            return cmsCore::error404();
        }

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        if ($ctype['is_in_groups_only']) {
            return cmsCore::error404();
        }

        $item = $this->model_content->getContentItem($ctype['name'], $item_id);
        if (!$item) {
            return cmsCore::error404();
        }

        if ($this->request->has('submit')) {

            if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
                return cmsCore::error404();
            }

            $this->model_content->unbindParent($ctype_name, $item_id);

            // уведомляем владельца группы
            if (!$group['access']['is_owner']) {

                $author = $this->model_users->getUser($group['owner_id']);
                if ($author) {

                    $this->controller_messages->addRecipient($author['id'])->sendNoticePM([
                        'content' => sprintf(
                            LANG_GROUPS_UNBIND_PM,
                            href_to_profile($this->cms_user),
                            $this->cms_user->nickname,
                            $group['title'],
                            $ctype['labels']['one'],
                            href_to($ctype['name'], $item['slug'] . '.html'),
                            $item['title']
                        )
                    ]);
                }
            }

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_UNBIND_SUCCESS, $group['title']));

            return $this->redirect(href_to($ctype['name'], $item['slug'] . '.html'));

        } else {

            return $this->cms_template->render('group_unbind', [
                'form_action'  => href_to($this->name, $group['slug'], ['unbind', $ctype_name, $item_id]),
                'confirm_text' => sprintf(LANG_GROUPS_UNBIND_CONFIRM, $ctype['labels']['one'], $group['title']),
                'group'        => $group
            ]);
        }
    }

}
