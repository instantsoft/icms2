<?php

class onLanguagesFrontpageActionIndex extends cmsAction {

    public function run($data) {

        $this->addHreflangTags();

        return $data;
    }

}
