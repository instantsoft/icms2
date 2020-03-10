<?php

class onAuthgaControllerAuthAfterSaveOptions extends cmsAction {

    public function run($options) {

        if($options['2fa']){
            foreach ($options['2fa'] as $twofa) {
                if($twofa == $this->name){
                    if(!$this->cms_core->db->isFieldExists('{users}', 'ga_secret')){
                        $this->cms_core->db->query("ALTER TABLE `{users}` ADD `ga_secret` VARCHAR(32) NULL DEFAULT NULL");
                    }
                    break;
                }
            }
        }

        return $options;

    }

}
