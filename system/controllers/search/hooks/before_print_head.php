<?php

class onSearchBeforePrintHead extends cmsAction {

    public function run($template){

<<<<<<< HEAD
        $template->addHead('<link rel="search" type="application/opensearchdescription+xml" href="'.href_to('search', 'opensearch').'" title="'.htmlspecialchars(sprintf(LANG_SEARCH_ON, cmsConfig::get('sitename'))).'" />');
=======
        $template->addHead('<link rel="search" type="application/opensearchdescription+xml" href="'.href_to('search', 'opensearch').'" title="'.html(sprintf(LANG_SEARCH_ON, cmsConfig::get('sitename')), false).'" />');
>>>>>>> origin/master

        return $template;

    }

}
