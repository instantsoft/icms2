<?php
class widgetAuth extends cmsWidget {

	public $is_cacheable = false;

    public function run(){

        if (cmsUser::isLogged()){ return false; }

        return array();

    }

}
