<?php
/**
 * @property \modelTags $model
 */
class actionTagsRecount extends cmsAction {

    public function run() {

        $this->model->recountTagsFrequency();

        return $this->redirectToAction();
    }

}
