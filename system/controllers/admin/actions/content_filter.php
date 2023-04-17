<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminContentFilter extends cmsAction {

    public function run($ctype_id) {

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $fields = $this->model_backend_content->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        if ($ctype['is_rating']) {
            $fields[] = [
                'title'   => LANG_RATING,
                'name'    => 'rating',
                'handler' => new fieldNumber('rating')
            ];
        }

        if ($ctype['is_comments']) {
            $fields[] = [
                'title'   => LANG_COMMENTS,
                'name'    => 'comments',
                'handler' => new fieldNumber('comments')
            ];
        }

        if (!empty($ctype['options']['hits_on'])) {
            $fields[] = [
                'title'   => LANG_HITS,
                'name'    => 'hits_count',
                'handler' => new fieldNumber('hits_count')
            ];
        }

        $fields[] = [
            'title'   => LANG_CP_SHOW_ONLY_IN_TRASH,
            'name'    => 'is_deleted',
            'handler' => new fieldCheckbox('is_deleted')
        ];

        list($fields, $ctype) = cmsEventsManager::hook('admin_content_filter', [$fields, $ctype]);
        list($fields, $ctype) = cmsEventsManager::hook('admin_content_' . $ctype['name'] . '_filter', [$fields, $ctype]);

        return $this->cms_template->render('grid_advanced_filter', [
            'fields' => $fields
        ]);
    }

}
