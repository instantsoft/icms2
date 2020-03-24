<?php

class actionAdminContentFilter extends cmsAction {

    public function run($ctype_id){

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $datasets = $content_model->getContentDatasets($ctype_id);

        $fields  = $content_model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

		if ($ctype['is_rating']){
			$fields[] = array(
				'title'   => LANG_RATING,
                'name'    => 'rating',
                'handler' => new fieldNumber('rating')
            );
		}

		if ($ctype['is_comments']){
			$fields[] = array(
				'title'   => LANG_COMMENTS,
                'name'    => 'comments',
                'handler' => new fieldNumber('comments')
            );
		}

		if (!empty($ctype['options']['hits_on'])){
			$fields[] = array(
				'title'   => LANG_HITS,
                'name'    => 'hits_count',
                'handler' => new fieldNumber('hits_count')
            );
		}

        list($fields, $ctype) = cmsEventsManager::hook('admin_content_filter', array($fields, $ctype));
        list($fields, $ctype) = cmsEventsManager::hook('admin_content_'.$ctype['name'].'_filter', array($fields, $ctype));

        $diff_order = cmsUser::getUPS('admin.grid_filter.content.diff_order');

        return $this->cms_template->render('content_filter', array(
            'ctype'      => $ctype,
            'datasets'   => $datasets,
            'fields'     => $fields,
            'diff_order' => $diff_order
        ));

    }

}
