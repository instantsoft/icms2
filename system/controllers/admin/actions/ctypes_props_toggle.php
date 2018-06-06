<?php

class actionAdminCtypesPropsToggle extends cmsAction {

    public function run($ctype_id, $prop_id){

        if (!$ctype_id || !$prop_id) {
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) {
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$prop = $content_model->getContentProp($ctype['name'], $prop_id);
        if (!$prop) {
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$is_in_filter = $prop['is_in_filter'] ? 0 : 1;

		$content_model->toggleContentPropFilter($ctype['name'], $prop_id, $is_in_filter);

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $is_in_filter
		));

    }

}
