<?php
class redirect extends cmsFrontend {

//============================================================================//
//============================================================================//

	public function actionIndex(){

        $url = $this->request->get('url');

        if (!$url) { cmsCore::error404(); }

        $this->redirect($url);

  	}

}
