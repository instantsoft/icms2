<?php

class backendComplaints extends cmsBackend{   

    public function actionIndex(){ 
        
        $this->redirectToAction('log');
        
    }
    
    public function deleteController($id) {
        
        $lang_dir       = 'system/languages/ru/controllers/complaints/';
        $controler_dir  = 'system/controllers/complaints/';
        $template_dir   = 'templates/default/controllers/complaints/';
        
       // files_remove_directory
 
    
    return parent::deleteController($id);
 
}
}