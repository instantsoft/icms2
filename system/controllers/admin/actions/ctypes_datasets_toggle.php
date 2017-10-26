<?php

class actionAdminCtypesDatasetsToggle extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) {
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$dataset = $this->model_content->getContentDataset($dataset_id);
        if (!$dataset) {
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$is_visible = $dataset['is_visible'] ? 0 : 1;

		$this->model_content->toggleContentDatasetVisibility($dataset_id, $is_visible);

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $is_visible
		));

    }

}
