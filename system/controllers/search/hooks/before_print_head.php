<?php

class onSearchBeforePrintHead extends cmsAction {

    public function run($template) {

        $template->addHead('<link rel="search" type="application/opensearchdescription+xml" href="' . href_to_abs('search', 'opensearch') . '" title="' . html(sprintf(LANG_SEARCH_ON, $this->cms_config->sitename), false) . '" />');

        return $template;
    }

}
