<?php

class actionContentWidgetCatsPresetsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }

        $presets = cmsCore::getModel('images')->getPresetsList();
        $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;

		$ctype_name = $this->request->get('value', '');

        if($ctype_name){
            $ctype = $this->model->getContentTypeByName($ctype_name);
            if (!$ctype) { cmsCore::error404(); }
        } else {
            return $this->cms_template->renderJSON($presets);
        }

        $_presets = array();

		if ($presets && !empty($ctype['options']['cover_sizes'])){
			foreach($presets as $key => $name){
                if(in_array($key, $ctype['options']['cover_sizes'])){
                    $_presets[$key] = $name;
                }
			}
		}

		return $this->cms_template->renderJSON($_presets ? $_presets : $presets);

    }

}
