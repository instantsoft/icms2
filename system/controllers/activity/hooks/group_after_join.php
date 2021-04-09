<?php

class onActivityGroupAfterJoin extends cmsAction {

    public function run($data) {

        list($group, $invite) = $data;

        $this->addEntry('groups', 'join', [
            'subject_title' => $group['title'],
            'subject_id'    => $group['id'],
            'subject_url'   => href_to_rel('groups', $group['slug']),
            'group_id'      => $group['id']
        ]);

        return [$group, $invite];
    }

}
