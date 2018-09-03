<?php

class sitemap extends cmsFrontend {

    protected $useOptions = true;

    public function routeAction($action_name){

        if($this->isActionExists($action_name)){
            return $action_name;
        }

        array_unshift($this->current_params, $action_name);

        return 'index';

    }

}
