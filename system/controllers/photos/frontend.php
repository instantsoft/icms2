<?php
class photos extends cmsFrontend {

    public static $row_height = null;
    public static $preset_small = null;

    protected $useOptions = true;
    public $useSeoOptions = true;

    public function route($uri) {

        $action_name = $this->parseRoute($this->cms_core->uri);
        if (!$action_name) {
            cmsCore::error404();
        }

        $this->runAction($action_name);
    }

    public function getOptions() {
        return $this->model->config;
    }

    public function getRowHeight($preset_options_name = 'preset_small') {

        if (isset(self::$row_height)) {
            return self::$row_height;
        }

        $preset_small = ['width' => 160, 'height' => 160];

        if (!empty($this->options[$preset_options_name])) {
            $preset_small = cmsCore::getModel('images')->getPresetByName($this->options[$preset_options_name]);
        }

        self::$row_height   = ($preset_small['height'] ? $preset_small['height'] : $preset_small['width']);
        self::$preset_small = $preset_small['name'];

        return self::$row_height;
    }

    public function getPhotosList($item_type_id = 0, $item_type = '') {

        cmsEventsManager::hook('photos_list_filter', $this->model);

        $photos = $this->model->getPhotos($item_type_id, $item_type);
        if (!$photos) {
            return false;
        }

        return cmsEventsManager::hook('photos_before_list', $photos);
    }

    public function renderPhotosList($item, $item_type, $page, $perpage = false, $show_next = true) {

        $perpage = $perpage ?: ($this->options['limit'] ?? 16);

        if (!$this->model->order_by) {
            $this->model->orderBy($this->options['ordering'], $this->options['orderto']);
        }

        if ($show_next) {
            // получаем на одну страницу больше
            $this->model->limitPagePlus($page, $perpage);
        } else {
            $this->model->limit($perpage);
        }

        // если альбом не общий, фильтруем для всех и для друзей
        if (empty($item['is_public']) && $this->cms_user->isFriend($item['user_id'])) {
            $this->model->disablePrivacyFilterForFriends();
        }

        $photos = $this->getPhotosList($item['id'], $item_type);
        if (!$photos && $page > 1) {
            cmsCore::error404();
        }

        if ($show_next && $photos && (count($photos) > $perpage)) {
            $has_next = true;
            array_pop($photos);
        } else {
            $has_next = false;
        }

        $is_owner = cmsUser::isAllowed('albums', 'delete', 'all') ||
                (cmsUser::isAllowed('albums', 'delete', 'own') && $item['user_id'] == $this->cms_user->id);

        $tpl_data = [
            'row_height'   => $this->getRowHeight(),
            'user'         => $this->cms_user,
            'item'         => $item,
            'photos'       => $photos,
            'page'         => $page,
            'has_next'     => $has_next,
            'is_owner'     => $is_owner,
            'item_type'    => $item_type,
            'preset_small' => $this->options['preset_small']
        ];

        if (!$this->request->isAjax()) {
            return $this->cms_template->renderInternal($this, 'album', $tpl_data);
        }

        return $this->halt($this->cms_template->renderInternal($this, 'photos', $tpl_data));
    }

    public function getDownloadHash($photo_id, $expires = 900) {

        $expires = time() + $expires;

        $data = $this->prepareHashString($photo_id, $expires);

        $signature = hash_hmac('sha256', $data, $this->cms_config->server_secret);

        return $expires . '.' . $signature;
    }

    public function validateDownloadHash($token, $photo_id) {

        $parts = explode('.', $token, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$expires, $signature] = $parts;

        if (!ctype_digit($expires)) {
            return false;
        }

        if ((int) $expires < time()) {
            return false;
        }

        $data = $this->prepareHashString($photo_id, $expires);

        $expected = hash_hmac('sha256', $data, $this->cms_config->server_secret);

        return hash_equals($expected, $signature);
    }

    private function prepareHashString($photo_id, $expires) {
        return cmsUser::getIp() . '|' . $photo_id . '|' . $expires . '|' . $this->cms_config->host;
    }

    public function validate_rating_score($score) {
        return $score >= 1 && $score <= 5;
    }

}
