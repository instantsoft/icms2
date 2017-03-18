<?php

class actionAdminControllersEventsUpdate extends cmsAction {

    public function run(){

        $events = array();

        $controllers_events = $this->model->getEvents();

        foreach($controllers_events as $controller){

            $events[ $controller['listener'] ][] = $controller['event'];

        }

        $manifests_events = cmsCore::getManifestsEvents();

        foreach ($manifests_events as $controller => $hooks){

            foreach ($hooks as $event){

                if (!empty($events[$controller])){

                    $key = array_search($event, $events[$controller]);

                    if ($key !== false){

                        unset($events[$controller][$key]);
                        continue;

                    }

                }

                $this->model->addEvent($controller, $event);

            }

            if (empty($events[$controller])){ unset($events[$controller]); }

        }

        if (!empty($events)){

            foreach ($events as $controller => $hooks){

                foreach ($hooks as $event){

                    $this->model->deleteEvent($controller, $event);

                }

            }

        }

        $this->redirectBack();
        $this->halt();

    }

}