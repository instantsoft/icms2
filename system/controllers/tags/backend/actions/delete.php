<?php

class actionTagsDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'tags';
        $this->cache_key   = 'tags.tags';
        $this->success_url = $this->cms_template->href_to('');

        $this->delete_callback = function($item, $model){

            $model->filterEqual('tag_id', $item['id'])->deleteFiltered('tags_bind');

            return true;
        };

    }

}
