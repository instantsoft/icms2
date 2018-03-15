<?php

class actionCommentsTrack extends cmsAction {

    public $request_params = array(
        'tc' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname')
            )
        ),
        'ts' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname')
            )
        ),
        'ti' => array(
            'default' => 0,
            'rules'   => array(
                array('required'),
                array('digits')
            )
        ),
        'is_track' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        )
    );

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if(!$this->cms_user->is_logged){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        if(cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma)){
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $target_controller = $this->request->get('tc');
        $target_subject    = $this->request->get('ts');
        $target_id         = $this->request->get('ti');
        $is_track          = $this->request->get('is_track');

        $success = $this->model->
                            filterEqual('target_controller', $target_controller)->
                            filterEqual('target_subject', $target_subject)->
                            filterEqual('target_id', $target_id)->
                            toggleTracking($is_track, $this->cms_user->id, $target_controller, $target_subject, $target_id);

        return $this->cms_template->renderJSON(array('error' => !$success));

    }

}
