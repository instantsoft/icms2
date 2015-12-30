<?php

class actionComplaintsLogAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }
		
        $grid = $this->loadDataGrid('log');

        $model = cmsCore::getModel('complaints');
                  
        $complaints = $model->getComplaints();
        
        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $complaints);

        $this->halt();

    }

}
