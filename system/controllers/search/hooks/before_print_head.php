<?php

class onSearchBeforePrintHead extends cmsAction {

    public function run($template){

        $template->addHead('<link rel="search" type="application/opensearchdescription+xml" href="'.href_to('search', 'opensearch').'" title="'.html(sprintf(LANG_SEARCH_ON, cmsConfig::get('sitename')), false).'" />');

        return $template;

    }

}
