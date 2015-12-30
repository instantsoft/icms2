<?php

class actionSearchOpensearch extends cmsAction {

    public function run(){

        header('Content-Type: text/xml; charset=utf-8');

        cmsTemplate::getInstance()->renderPlain('opensearch', array(
            'site_config' => cmsConfig::getInstance()
        ));

    }

}

