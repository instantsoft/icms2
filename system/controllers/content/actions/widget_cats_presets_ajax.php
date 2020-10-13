<?php

class actionContentWidgetCatsPresetsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax() || !cmsUser::isAdmin()){ return cmsCore::error404(); }

        $presets = cmsCore::getModel('images')->getPresetsList();
        $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;

		$ctype_name = $this->request->get('value', '');

        if($ctype_name){
            $ctype = $this->model->getContentTypeByName($ctype_name);
            if (!$ctype) {
                return $this->cms_template->renderJSON(['' => '']);
            }
        } else {
            return $this->cms_template->renderJSON($presets);
        }

        $_presets = [];

		if ($presets && !empty($ctype['options']['cover_sizes'])){
			foreach($presets as $key => $name){
                if(in_array($key, $ctype['options']['cover_sizes'])){
                    $_presets[] = ['title'=>$name, 'value'=>$key];
                }
			}
		}

		return $this->cms_template->renderJSON(['' => ''] + ($_presets ? $_presets : $presets));

    }

}
