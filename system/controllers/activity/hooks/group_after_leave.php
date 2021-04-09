<?php

class onActivityGroupAfterLeave extends cmsAction {

    public function run($group) {

        $this->addEntry('groups', 'leave', [
            'subject_title' => $group['title'],
            'subject_id'    => $group['id'],
            'subject_url'   => href_to_rel('groups', $group['slug']),
            'group_id'      => $group['id']
        ]);

        return $group;
    }

}
