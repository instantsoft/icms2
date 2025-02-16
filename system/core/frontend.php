<?php

class cmsFrontend extends cmsController {

    public function executeAction($action_name, $params = []) {

        if ($this->useSeoOptions && $action_name === 'index') {

            $default = string_lang($this->name . '_CONTROLLER');

            $this->cms_template->
                    setPagePatternKeywords(['keys' => $default], 'keys', 'seo_keys')->
                    setPagePatternH1(['title' => $default], 'title', 'seo_h1')->
                    setPagePatternTitle(['title' => $default], 'title', 'seo_title')->
                    setPagePatternDescription(['description' => $default], 'description', 'seo_desc');
        }

        return parent::executeAction($action_name, $params);
    }

}
