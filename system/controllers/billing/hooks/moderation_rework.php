<?php

class onBillingModerationRework extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($data) {

        [$target_name, $item, $task] = $data;

        $this->unHoldTargetItem($target_name, $item);

        return $data;
    }

}
