<?php

class actionGroupsGroupJoin extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group) {

        if ($group['access']['is_member']) {
            $this->redirectToAction($group['slug']);
        }

        if (!$group['access']['is_can_join']) {
            cmsCore::error404();
        }

        $result = cmsEventsManager::hook('group_before_join', [
            'allow'  => true,
            'group'  => $group,
            'invite' => $group['access']['invite']
        ]);

        if (!$result['allow']) {

            if (isset($result['access_text'])) {

                cmsUser::addSessionMessage($result['access_text'], 'error');

                if (isset($result['redirect_url'])) {
                    $this->redirect($result['redirect_url']);
                } else {
                    $this->redirectToAction($group['slug']);
                }
            }

            cmsCore::error404();
        }

        $group  = $result['group'];
        $invite = $result['invite'];

        $this->model->addMembership($group['id'], $this->cms_user->id);

        // роли по умолчанию
        if (!empty($group['join_roles'])) {
            $this->model->setUserRoles($group['id'], $group['join_roles'], $this->cms_user->id);
        }

        if ($invite) {
            $this->model->deleteInvite($invite['id']);
        }

        list($group, $invite) = cmsEventsManager::hook('group_after_join', [$group, $invite]);

        cmsUser::addSessionMessage(LANG_GROUPS_JOIN_MESSAGE, 'success');

        $this->redirectToAction($group['slug']);
    }

}
