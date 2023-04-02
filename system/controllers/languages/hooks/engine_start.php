<?php

class onLanguagesEngineStart extends cmsAction {

    public function run() {

        $browser_language = cmsCore::getBrowserLanguage();

        if($browser_language &&
                !cmsUser::getCookie('is_not_first_visit') &&
                !in_array($browser_language, [$this->cms_config->language, cmsCore::getLanguageName()]) &&
                is_dir($this->cms_config->root_path . 'system/languages/' . $browser_language . '/')){

            // Чтобы могли менять язык вручную
            cmsUser::setCookie('is_not_first_visit', 1, 31536000);

            return $this->redirect($this->cms_config->root . $browser_language.'/' . $this->cms_core->uri.($this->cms_core->uri_query ? '?'.http_build_query($this->cms_core->uri_query) : ''));
        }

        return true;
    }

}
