<?php

class onBillingModerationCancel extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($data) {

        [$moderator, $target_name, $item, $task, $author] = $data;

        $this->unHoldTargetItem($target_name, $item);

        return $data;
    }

}
