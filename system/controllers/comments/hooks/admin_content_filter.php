<?php

class onCommentsAdminContentFilter extends cmsAction {

    public function run($data) {

        list($fields, $ctype) = $data;

        if ($ctype['is_comments']) {
            $fields[] = [
                'title'   => LANG_COMMENTS,
                'name'    => 'comments',
                'handler' => new fieldNumber('comments')
            ];
        }

        return [$fields, $ctype];
    }

}
