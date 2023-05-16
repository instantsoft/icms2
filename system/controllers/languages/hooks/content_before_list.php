<?php

class onLanguagesContentBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if (!empty($ctype['options']['is_multilanguages'])) {
            $this->addHreflangTags();
        }

        return [$ctype, $items];
    }

}
