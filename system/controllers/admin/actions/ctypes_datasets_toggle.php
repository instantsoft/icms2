<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesDatasetsToggle extends cmsAction {

    public function run($dataset_id = null) {

        if (!$dataset_id) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $dataset = $this->model_backend_content->getContentDataset($dataset_id);

        if (!$dataset) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $is_visible = $dataset['is_visible'] ? 0 : 1;

        $this->model_backend_content->toggleContentDatasetVisibility($dataset_id, $is_visible);

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => $is_visible
        ]);
    }

}
