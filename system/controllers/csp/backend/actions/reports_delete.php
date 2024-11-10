<?php
/**
 * @property \cmsModel $model
 */
class actionCspReportsDelete extends cmsAction {

    public function run($id = null) {

        if ($id) {
            $items = [$id];
        } else {
            $items = $this->request->get('selected', []);
        }

        if (!$items) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        foreach ($items as $item_id) {
            if (!is_numeric($item_id)) {
                return cmsCore::error404();
            }
        }

        $this->model->filterIn('id', $items)->deleteFiltered('csp_logs');

        cmsUser::addSessionMessage(LANG_CSP_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('reports');
    }

}
