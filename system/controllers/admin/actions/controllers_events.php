<?php

class actionAdminControllersEvents extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('controllers_events');

        $diff_events = $this->getEventsDifferences();

        return $this->cms_template->render('controllers_events', array(
            'events_add'    => $diff_events['added'],
            'events_delete' => $diff_events['deleted'],
            'grid'          => $grid
        ));

    }

}
