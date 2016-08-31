<?php

class onActivityContentAfterUpdateApprove extends cmsAction {

    public function run($data){

        $ctype_name = $data['ctype_name'];
        $item = $data['item'];

        // обновляем запись в ленте активности
        $this->updateEntry('content', "add.{$ctype_name}", $item['id'], array(
            'subject_title' => $item['title'],
            'subject_id'    => $item['id'],
            'subject_url'   => href_to_rel($ctype_name, $item['slug'] . '.html'),
            'is_private'    => isset($item['is_private']) ? $item['is_private'] : 0,
            'is_pub'        => $item['is_pub']
        ));

        return $data;

    }

}
