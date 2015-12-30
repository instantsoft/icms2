<?php

class onComplaintsBeforePrintHead extends cmsAction {

    public function run($head){
            
        $head->addJSFromContext("templates/{$head->name}/controllers/complaints/complaints.js");

    return $head;

    }

}
