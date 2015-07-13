<?php

class frontpage extends cmsFrontend {

	public function actionIndex(){

		$config = cmsConfig::getInstance();
		$template = cmsTemplate::getInstance();
		
        $mode = $config->frontpage;		
		$title = $config->hometitle;
		
		if ($title){
			$template->setFrontPageTitle($title);
		}

		$is_no_def_meta = isset($config->is_no_meta) ? $config->is_no_meta : false;
		
		if ($is_no_def_meta){
			$template->setPageKeywords($config->metakeys);
			$template->setPageDescription($config->metadesc);
		}		

        //
        // Только виджеты
        //
        if (!$mode || $mode == 'none') {

            return false;

        }

        //
        // Профиль / авторизация
        //
        if ($mode == 'profile'){

            $user = cmsUser::getInstance();

            if ($user->is_logged){ $this->redirectTo('users', $user->id); }

            $auth_controller = cmsCore::getController('auth', new cmsRequest(array(
                'is_frontpage' => true
            )));

            return $auth_controller->runAction('login');

        }

        //
        // Контент
        //
        if (mb_strstr($mode, 'content:')){

            list($mode, $ctype_name) = explode(':', $mode);

			$request_data = $this->request->getData();
			
			$request_data['ctype_name'] = $ctype_name;
			$request_data['slug'] = 'index';
			$request_data['is_frontpage'] = true;			
			
            $request = new cmsRequest($request_data);
			
            $content_controller = cmsCore::getController('content', $request);

            return $content_controller->runAction('category_view');

        }

	}

}
