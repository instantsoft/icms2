<?php

class actionTagsDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'tags';
        $this->cache_key   = 'tags.tags';
        $this->success_url = $this->cms_template->href_to('');

        $this->delete_callback = function ($item, $model) {

            $targets = $model->filterEqual('tag_id', $item['id'])->get('tags_bind') ?: [];

            $model->filterEqual('tag_id', $item['id'])->deleteFiltered('tags_bind');

            foreach ($targets as $target) {

                $tags = $model->getTagsForTarget($target['target_controller'], $target['target_subject'], $target['target_id']);

                $model_target = cmsCore::getModel($target['target_controller']);

                if (!method_exists($model_target, 'updateContentItemTags')) {
                    continue;
                }

                $model_target->updateContentItemTags($target['target_subject'], $target['target_id'], implode(', ', $tags));
            }

            return true;
        };

    }

}
