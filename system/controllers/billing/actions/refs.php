<?php
/**
 * @property \modelBilling $model
 * @property \modelUsers $model_users
 */
class actionBillingRefs extends cmsAction {

    public function run($user_id = false) {

        if (!$this->options['is_refs']) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if ($user_id && $user_id != $this->cms_user->id && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        if (!$user_id) {
            $user_id = $this->cms_user->id;
        }

        $user = $this->model_users->getUser($user_id);
        if (!$user) {
            return cmsCore::error404();
        }

        $page    = $this->request->get('page', 1);
        $perpage = $this->options['limit_refs'];

        $max_level = !empty($this->options['ref_levels']) ? count($this->options['ref_levels']) : 0;
        $type      = $this->options['ref_type'];

        $this->model->filterEqual('ref_id', $user_id)->orderBy('id', 'desc');

        if ($type !== 'collect') {

            $this->model->limitPage($page, $perpage);

        } else {

            $this->model->limit(false);

            $perpage = 1000000;
            $page    = 1;
        }

        $total = $this->model->getReferalsCount();
        $refs  = $this->model->getReferals($max_level);

        return $this->cms_template->render([
            'terms_url'      => $this->options['ref_terms'] ? rel_to_href($this->options['ref_terms']) : '',
            'ref_bonus'      => $this->options['ref_bonus'],
            'ref_levels'     => $this->options['ref_levels'],
            'ref_mode'       => $this->options['ref_mode'],
            'b_spellcount'   => $this->options['currency'],
            'ref_url'        => sprintf(href_to_abs('r', '%d'), $user['id']),
            'total'          => $total,
            'refs'           => $refs,
            'page'           => $page,
            'is_own_profile' => $user_id == $this->cms_user->id,
            'perpage'        => $perpage,
            'user'           => $user,
            'type'           => $type,
            'scale'          => $this->options['ref_scale']
        ]);
    }

}
