<?php

class actionManifestsIndex extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('manifests');

        $hooks_in_db = $this->getHooksInDB();
        $hooks_in_files = $this->getHooksInFiles();

        foreach ($hooks_in_db as $controller => $hooks){

            foreach ($hooks as $k => $event_name){

                if (!empty($hooks_in_files[$controller])){

                    $key = array_search($event_name, $hooks_in_files[$controller]);

                    if ($key !== false){

                        unset($hooks_in_files[$controller][$key]);
                        unset($hooks_in_db[$controller][$k]);

                    }

                }

            }

            if (empty($hooks_in_files[$controller])){ unset($hooks_in_files[$controller]); }
            if (empty($hooks_in_db[$controller])){ unset($hooks_in_db[$controller]); }

        }

        return cmsTemplate::getInstance()->render('index', array(
            'events_add' => $hooks_in_files,
            'events_delete' => $hooks_in_db,
            'grid' => $grid
        ));

    }

}