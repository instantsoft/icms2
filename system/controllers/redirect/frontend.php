<?php
class redirect extends cmsFrontend {

	public function actionIndex(){

        header('X-Frame-Options: DENY');

        $url = $this->request->get('url');
        if (!$url) { cmsCore::error404(); }

        $this->redirect($url);

  	}

}
