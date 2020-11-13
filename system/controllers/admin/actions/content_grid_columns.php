<?php

class actionAdminContentGridColumns extends cmsAction {

    public function run($ctype_id){

        if( !$this->request->isAjax()
                ||
            !is_numeric($ctype_id)
        ){ return cmsCore::error404(); }

        $items = $this->getContentGridColumnsSettings($ctype_id);

        if(!$items){ return cmsCore::error404(); }

        if($this->request->has('submit')){

            $new_config = $this->request->get('columns', array());

            cmsUser::setUPS('admin.grid_columns.content.'.$ctype_id, $new_config);
            cmsUser::setUPS('admin.grid_columns.content.'.$ctype_id.'.changed', true);

            return $this->cms_template->renderJSON(array(
                'error' => false
            ));

        }

        if($this->request->has('reset')){

            cmsUser::deleteUPS('admin.grid_columns.content.'.$ctype_id);

            return $this->cms_template->renderJSON(array(
                'error' => false
            ));

        }

        $default = $this->getContentGridColumnsSettingsDefault();

        $saved = cmsUser::getUPS('admin.grid_columns.content.'.$ctype_id)?:array();

        $config = array_merge($default, $saved);

        return $this->cms_template->render('content_grid_columns', array(
            'items'     => $items,
            'config'    => $config,
            'ctype_id'  => $ctype_id
        ));

    }

}
