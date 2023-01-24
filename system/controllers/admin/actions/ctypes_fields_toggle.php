<?php
/**
 * @property \modelContent $model_content
 */
class actionAdminCtypesFieldsToggle extends cmsAction {

    public function run($mode = null, $ctype_id = null, $field_id = null) {

        $modes = ['list' => 'is_in_list', 'item' => 'is_in_item', 'enable' => 'is_enabled'];

        if (!isset($modes[$mode]) || !$ctype_id || !$field_id) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $ctype = $this->model_content->getContentType($ctype_id);

        if (!$ctype) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $field = $this->model_content->getContentField($ctype['name'], $field_id);

        if (!$field) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $visibility_field = $modes[$mode];

        $is_visible = $field[$visibility_field] ? 0 : 1;

        $this->model_content->toggleContentFieldVisibility($ctype['name'], $field_id, $visibility_field, $is_visible);

        if ($is_visible && $mode !== 'enable' && !empty($field['options']['context_list']) && array_search('0', $field['options']['context_list']) === false) {
            $is_visible = -1;
        }

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => $is_visible
        ]);
    }

}
