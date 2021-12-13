<?php

class onSearchPhotosBeforeItem extends cmsAction {

    public function run($data) {

        if (empty($this->options['is_hash_tag'])) {
            return $data;
        }

        list($photo, $album, $ctype) = $data;

        $photo['content'] = $this->parseHashTag($photo['content']);

        return [$photo, $album, $ctype];
    }

}
