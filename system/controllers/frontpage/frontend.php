<?php

class frontpage extends cmsFrontend {

	public function actionIndex(){

		if ($this->cms_config->hometitle){
			$this->cms_template->setFrontPageTitle($this->cms_config->hometitle);
		}

		$is_no_def_meta = isset($this->cms_config->is_no_meta) ? $this->cms_config->is_no_meta : false;

		if ($is_no_def_meta){
			$this->cms_template->setPageKeywords($this->cms_config->metakeys);
			$this->cms_template->setPageDescription($this->cms_config->metadesc);
		}

        //
        // Только виджеты
        //
        if (!$this->cms_config->frontpage || $this->cms_config->frontpage == 'none') {

            return false;

        }

        //
        // Экшен контроллера
        //

        list($controller_name, $action) = explode(':', $this->cms_config->frontpage);

        if(!cmsController::enabled($controller_name)){

            return false;

        }

        $this->request->set('is_frontpage', true);

        $controller = cmsCore::getController($controller_name, $this->request);

        return $controller->runHook('frontpage', array('action' => $action));

	}

}
