<?php

class actionCommentsTrackStop extends cmsAction {

    public function run($target_controller, $target_subject, $target_id){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        $track = $this->model->getTracking($this->cms_user->id, $target_controller, $target_subject, $target_id);

        if ($track){ $this->model->deleteTracking($track['id']); }

        return true;

    }

}
