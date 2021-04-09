<?php

class onActivityGroupsAfterAcceptRequest extends cmsAction {

    public function run($data) {

        list($group, $invited_id) = $data;

        $this->addEntry('groups', 'join', [
            'subject_title' => $group['title'],
            'subject_id'    => $group['id'],
            'subject_url'   => href_to_rel('groups', $group['slug']),
            'group_id'      => $group['id'],
            'user_id'       => $invited_id
        ]);

        return [$group, $invited_id];
    }

}
