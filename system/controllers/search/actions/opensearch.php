<?php

class actionSearchOpensearch extends cmsAction {

    public function run(){

        header('Content-Type: text/xml; charset=utf-8');

<<<<<<< HEAD
        cmsTemplate::getInstance()->renderPlain('opensearch', array(
            'site_config' => cmsConfig::getInstance()
        ));

    }

}

=======
        return $this->cms_template->renderPlain('opensearch', array(
            'site_config' => $this->cms_config
        ));

    }

}
>>>>>>> origin/master
