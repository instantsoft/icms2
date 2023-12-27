<?php

class onActivityPublishDelayedContent extends cmsAction {

    public function run($data) {

        foreach ($data as $ctype_name => $items) {

            foreach ($items as $item) {

                $this->addEntry('content', "add.{$ctype_name}", [
                    'user_id'          => $item['user_id'],
                    'subject_title'    => $item['title'],
                    'subject_id'       => $item['id'],
                    'subject_url'      => href_to_rel($ctype_name, $item['slug'] . '.html'),
                    'is_private'       => isset($item['is_private']) ? $item['is_private'] : 0,
                    'group_id'         => isset($item['parent_id']) ? $item['parent_id'] : null,
                    'is_parent_hidden' => $item['is_parent_hidden'],
                    'date_pub'         => $item['date_pub'],
                    'is_pub'           => 1
                ]);
            }
        }

        return $data;
    }

}
