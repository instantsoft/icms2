<?php

class onRatingAdminContentFilter extends cmsAction {

    public function run($data) {

        list($fields, $ctype) = $data;

        if ($ctype['is_rating']) {
            $fields[] = [
                'title'   => LANG_RATING,
                'name'    => 'rating',
                'handler' => new fieldNumber('rating')
            ];
        }

        return [$fields, $ctype];
    }

}
