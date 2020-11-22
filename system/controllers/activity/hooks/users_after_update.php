<?php

class onActivityUsersAfterUpdate extends cmsAction {

    public function run($data){

        list($profile, $old, $fields) = $data;

        // Постим уведомление о смене аватара в ленту
        if (!$this->isAvatarsEqual($profile['avatar'], $old['avatar'])){

            $this->deleteEntry('users', 'avatar', $profile['id']);

            if (!empty($profile['avatar'])){
                $this->addEntry('users', 'avatar', [
                    'user_id'       => $profile['id'],
                    'subject_title' => $profile['nickname'],
                    'subject_id'    => $profile['id'],
                    'subject_url'   => href_to_rel('users', (empty($profile['slug']) ? $profile['id'] : $profile['slug'])),
                    'is_private'    => 0,
                    'group_id'      => null,
                    'images'        => [
                        [
                            'url' => href_to_rel('users', (empty($profile['slug']) ? $profile['id'] : $profile['slug'])),
                            'src' => html_image_src($profile['avatar'], $fields['avatar']['options']['size_full'])
                        ]
                    ],
                    'images_count'  => 1
                ]);
            }
        }

        return [$profile, $old, $fields];
    }

    private function isAvatarsEqual($old, $new){

        if (!is_array($old)){ $old = cmsModel::yamlToArray($old); }
        if (!is_array($new)){ $new = cmsModel::yamlToArray($new); }

        return $old == $new;
    }

}
