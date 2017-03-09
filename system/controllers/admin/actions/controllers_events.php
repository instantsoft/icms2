<?php

class actionAdminControllersEvents extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('controllers_events');

        $events = array();

        $controllers_events = $this->model->getEvents();

        foreach($controllers_events as $controller){

            $events[ $controller['listener'] ][] = $controller['event'];

        }

        $manifests_events = cmsCore::getManifestsEvents();

        foreach ($events as $controller => $hooks){

            foreach ($hooks as $k => $event_name){

                if (!empty($manifests_events[$controller])){

                    $key = array_search($event_name, $manifests_events[$controller]);

                    if ($key !== false){

                        unset($manifests_events[$controller][$key]);
                        unset($events[$controller][$k]);

                    }

                }

            }

            if (empty($manifests_events[$controller])){ unset($manifests_events[$controller]); }
            if (empty($events[$controller])){ unset($events[$controller]); }

        }

        return $this->cms_template->render('controllers_events', array(
            'events_add' => $manifests_events,
            'events_delete' => $events,
            'grid' => $grid
        ));

    }

}