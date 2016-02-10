<?php

class actionAdminWidgetsAutocomplete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $term = $this->request->get('term');
		$widget_path = $this->request->get('widget_path');

        if (!$term) { cmsCore::error404(); }
		
        if (!$widget_path){
			$widget_path = 'templates/'.cmsConfig::get('template').'/widgets/';
			$default_path = 'templates/default/widgets/';
		} else {
			$widget_path = 'templates/'.cmsConfig::get('template').'/'.$widget_path.'/';
			$default_path = 'templates/default/'.$widget_path.'/';
		}

        $tpls = cmsCore::getFilesList($widget_path, '*.tpl.php');
		$default_tpls = cmsCore::getFilesList($default_path, '*.tpl.php');
		$tpls = array_unique(array_merge($tpls, $default_tpls));

        $result = array();

        if ($tpls){
            foreach($tpls as $tpl){
                $result[] = array(
                    'id' => str_replace('.tpl.php', '', $tpl),
                    'label' => str_replace('.tpl.php', '', $tpl),
                    'value' => str_replace('.tpl.php', '', $tpl)
                );
            }
        }

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
