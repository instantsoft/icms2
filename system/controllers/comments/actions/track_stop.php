<?php

class actionCommentsTrackStop extends cmsAction {

    public function run($target_controller, $target_subject, $target_id){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $track = $this->model->getTracking($user->id, $target_controller, $target_subject, $target_id);

        if ($track){ $this->model->deleteTracking($track['id']); }

        return true;

    }

}
