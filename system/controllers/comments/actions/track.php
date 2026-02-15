<?php

class actionCommentsTrack extends cmsAction {

    public $request_params = [
        'tc' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname']
            ]
        ],
        'ts' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname']
            ]
        ],
        'ti' => [
            'default' => 0,
            'rules'   => [
                ['required'],
                ['digits']
            ]
        ],
        'is_track' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ]
    ];

    public function run(){

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        if(cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma)){
            return $this->cms_template->renderJSON(['error' => true]);
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

        return $this->cms_template->renderJSON(['error' => !$success]);
    }

}
