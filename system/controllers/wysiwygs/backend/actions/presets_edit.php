<?php

class actionWysiwygsPresetsEdit extends cmsAction {

    public function run($id){

        return $this->runExternalAction('presets_add', $this->params);

    }

}
