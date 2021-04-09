<?php

class onActivityContentPhotosAfterAdd extends cmsAction {

    public function run($data) {

        list($photos, $album, $ctype) = $data;

        $activity_thumb_images = [];

        $photos_count = count($photos);
        if ($photos_count > 5) {
            $photos = array_slice($photos, 0, 4);
        }

        if ($photos_count) {
            foreach ($photos as $photo) {

                $_presets     = array_keys($photo['image']);
                $small_preset = end($_presets);

                $activity_thumb_images[] = [
                    'url'   => href_to_rel('photos', $photo['slug'] . '.html'),
                    'src'   => html_image_src($photo['image'], $small_preset),
                    'title' => $photo['title']
                ];
            }
        }

        $this->addEntry('photos', 'add.photos', [
            'user_id'       => $this->cms_user->id,
            'subject_title' => $album['title'],
            'subject_id'    => $album['id'],
            'subject_url'   => href_to_rel('albums', $album['slug'] . '.html'),
            'is_private'    => isset($album['is_private']) ? $album['is_private'] : 0,
            'group_id'      => isset($album['parent_id']) ? $album['parent_id'] : null,
            'images'        => $activity_thumb_images,
            'images_count'  => $photos_count
        ]);

        return [$photos, $album, $ctype];
    }

}
