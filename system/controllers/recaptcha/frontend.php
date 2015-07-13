<?php
class recaptcha extends cmsFrontend {

    protected $useOptions = true;

    public function includeRecaptchaLib(){
        
        $lib_file = 'system/controllers/recaptcha/lib/recaptchalib.php';
        
        cmsCore::includeFile($lib_file);
        
    }
    
}
