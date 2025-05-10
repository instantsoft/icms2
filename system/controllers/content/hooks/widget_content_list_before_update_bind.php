<?php

class onContentWidgetContentListBeforeUpdateBind extends cmsAction {

    public function run($data){

        list($widget_id, $widget, $template_name) = $data;

        // Тип контента должен быть указан
        if (!$widget['options']['ctype_id']) {
            return $data;
        }

        $ctype = $this->model->getContentType($widget['options']['ctype_id']);

        if (!$ctype) {
            return $data;
        }

        // Создаём фултекст индекс
        if ($widget['options']['widget_type'] === 'related' && $widget['options']['related_type'] === 'tags') {

            $this->model->db->addIndex($this->model->getContentTypeTableName($ctype['name']), 'tags', 'tags_fulltext', 'FULLTEXT');
        }

        return [$widget_id, $widget, $template_name];
    }

}
