<?php

class onFrontpageCtypeAfterUpdate extends cmsAction {

    public function run($ctype){

        $cfg = cmsConfig::getInstance();

        if ($cfg->frontpage == "content:{$ctype['name']}" && !$ctype['options']['list_on']){
            $cfg->update('frontpage', 'none');
        }
        
        return $ctype;

    }

}
