<?php
/**
 * @property \modelContent $model
 * @property \modelUsers $model_users
 * @property \messages $controller_messages
 */
class actionContentItemOwnerProcess extends cmsAction {

    public function run($ctype_name, $item_id, $owner_id, $action) {

        if (!$this->request->isInternal()) {
            return cmsCore::error404();
        }

        if (!$ctype_name || !$item_id || !$owner_id) {
            return false;
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return false;
        }

        $item = $this->model->getContentItem($ctype['name'], $item_id);
        if (!$item) {
            return false;
        }

        $user = $this->model_users->getUser($owner_id);
        if (!$user) {
            return false;
        }

        if ($item['user_id'] != $owner_id) {
            return false;
        }

        $ups_key = 'change_owner_' . $ctype['name'] . $item['id'] . '_' . $user['id'];

        if (!cmsUser::getUPS($ups_key)) {
            return false;
        }

        $item_link = '<a href="' . href_to($ctype['name'], $item['slug'] . '.html') . '">' . $item['title'] . '</a>';
        $user_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';

        if ($action === 'accept') {

            $this->model->updateContentItemOwner($ctype['name'], $item['id'], $this->cms_user->id);

            $this->controller_messages->addRecipient($this->cms_user->id);

            $notice = [
                'content' => sprintf(LANG_CHOWN_SUCCESS, $item_link),
                'options' => [
                    'is_closeable' => true
                ]
            ];

            $this->controller_messages->sendNoticePM($notice);
        }

        $this->controller_messages->clearRecipients()->addRecipient($owner_id);

        $notice = [
            'content' => sprintf(string_lang('LANG_CHOWN_NOTICE_' . $action), $item_link, $user_link),
            'options' => [
                'is_closeable' => true
            ]
        ];

        $this->controller_messages->sendNoticePM($notice);

        cmsUser::deleteUPS($ups_key);

        return true;
    }

}
