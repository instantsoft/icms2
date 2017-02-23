<?php

class modelManifests extends cmsModel{

    public function getManifests(){

        return $this->get('controllers_hooks');
    }

    public function reorderManifests($ids_list){

        $this->reorderByList('controllers_hooks', $ids_list);

        cmsCache::getInstance()->clean('events');

        return true;

    }

    public function addEvent($controller, $event_name) {

        return $this->insert('controllers_hooks', array(
            'controller' => $controller, 'name' => $event_name
        ));

    }

    public function deleteEvent($controller, $event_name) {

        return $this->filterEqual('controller', $controller)->
            filterEqual('name', $event_name)->
            deleteFiltered('controllers_hooks');

    }

}