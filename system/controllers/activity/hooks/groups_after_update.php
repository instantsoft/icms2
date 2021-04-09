<?php

class onActivityGroupsAfterUpdate extends cmsAction {

    public function run($group) {

        $update = ['subject_title' => $group['title']];
        if (!empty($group['slug'])) {
            $update['subject_url'] = href_to_rel('groups', $group['slug']);
        }

        $this->updateEntry('groups', 'join', $group['id'], $update);
        $this->updateEntry('groups', 'leave', $group['id'], $update);

        return $group;
    }

}
