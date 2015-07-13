<?php

class onFrontpageCtypeAfterDelete extends cmsAction {

    public function run($ctype){

        $cfg = cmsConfig::getInstance();

        if ($cfg->frontpage == "content:{$ctype['name']}"){
            $cfg->update('frontpage', 'none');
        }

        return $ctype;

    }

}
