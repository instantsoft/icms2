<?php
class actionCommentsIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'comments';
        $this->grid_name  = 'comments_list';

        $this->list_callback = function ($model) {

            $model->joinUserLeft();

            return $model;
        };
    }

}
