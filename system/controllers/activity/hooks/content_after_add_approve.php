<?php

class onActivityContentAfterAddApprove extends cmsAction {

    public function run($data){

        $ctype_name = $data['ctype_name'];
        $item = $data['item'];

        $subject_url = '';

        if(!empty($item['url'])){
            $subject_url = $item['url'];
        } else {
            $subject_url = href_to_rel($ctype_name, $item['slug'] . '.html');
        }

        $this->addEntry('content', "add.{$ctype_name}", array(
            'user_id'          => $item['user_id'],
            'subject_title'    => $item['title'],
            'subject_id'       => $item['id'],
            'subject_url'      => $subject_url,
            'is_private'       => isset($item['is_private']) ? $item['is_private'] : 0,
            'group_id'         => isset($item['parent_id']) ? $item['parent_id'] : null,
            'is_parent_hidden' => !empty($item['is_parent_hidden']) ? 1 : null,
            'is_pub'           => (isset($item['is_pub']) ? ($item['is_pub'] <= 0 ? 0 : 1) : 1)
        ));

        return $data;

    }

}
