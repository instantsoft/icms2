<?php

class onContentEngineStart extends cmsAction {

    public function run(){

        if(!$this->cms_config->ctype_default){
            return true;
        }

        if(!$this->cms_core->uri){
            return true;
        }

        $this->cms_core->defineController();

        if($this->cms_core->controller !== $this->name){
            return true;
        }

        if(!$this->cms_core->uri_action){
            return true;
        }

        // в типе контента дефис, значит это набор или категория
        if(strpos($this->cms_core->uri_action, '-') !== false && strpos($this->cms_core->uri_action, '.html') === false){

            list($ctype_name, $cat_slug) = explode('-', $this->cms_core->uri_action);

        } else {
            $ctype_name = $this->cms_core->uri_action;
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);

        if(!$ctype){

            $this->cms_core->uri_controller_before_remap = $this->cms_core->uri_controller;

            // передаём первый из списка
            $this->cms_core->uri = $this->cms_config->ctype_default[0].'/'.$this->cms_core->uri;

        }

        return true;

    }

}
