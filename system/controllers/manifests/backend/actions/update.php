<?php

class actionManifestsUpdate extends cmsAction {

    public function run(){

        $hooks_in_db = $this->getHooksInDB();

        $hooks_in_files = $this->getHooksInFiles();

        foreach ($hooks_in_files as $controller => $hooks){

            foreach ($hooks as $event_name){

                if (!empty($hooks_in_db[$controller])){

                    $key = array_search($event_name, $hooks_in_db[$controller]);

                    if ($key !== false){

                        unset($hooks_in_db[$controller][$key]);
                        continue;

                    }

                }

                $this->model->addEvent($controller, $event_name);

            }

            if (empty($hooks_in_db[$controller])){ unset($hooks_in_db[$controller]); }

        }

        if (!empty($hooks_in_db)){

            foreach ($hooks_in_db as $controller => $hooks){

                foreach ($hooks as $event_name){

                    $this->model->deleteEvent($controller, $event_name);

                }

            }

        }

        $this->redirectBack();
        $this->halt();

    }

}