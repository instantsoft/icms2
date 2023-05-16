<?php

class onLanguagesContentBeforeItem extends cmsAction {

    public function run($data) {

        list($ctype, $item, $fields) = $data;

        if (!empty($ctype['options']['is_multilanguages'])) {
            $this->addHreflangTags();
        }

        return [$ctype, $item, $fields];
    }

}
