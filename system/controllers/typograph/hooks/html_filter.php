<?php

class onTypographHtmlFilter extends cmsAction {

    public function run($data) {

        return $this->parseText($this->preparePresetParamsAndGetText($data));
    }

}
