<?php

class actionCommentsTrack extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if(!$this->cms_user->is_logged){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        if(cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma)){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $target_controller = $this->request->get('tc', '');
        $target_subject    = $this->request->get('ts', '');
        $target_id         = $this->request->get('ti', 0);
        $is_track          = $this->request->get('is_track', 0);

        if(!$target_controller || !$target_subject || !$target_id){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $is_valid = ($this->validate_sysname($target_controller)===true) &&
                    ($this->validate_sysname($target_subject)===true) &&
                    is_numeric($target_id) &&
                    is_numeric($is_track);

        if (!$is_valid){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $success = $this->model->
                            filterEqual('target_controller', $target_controller)->
                            filterEqual('target_subject', $target_subject)->
                            filterEqual('target_id', $target_id)->
                            toggleTracking($is_track, $this->cms_user->id, $target_controller, $target_subject, $target_id);

        return $this->cms_template->renderJSON(array('error' => !$success));

    }

}
