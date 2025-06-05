<?php
/**
 * @property \modelContent $model
 * @property \modelUsers $model_users
 * @property \messages $controller_messages
 */
class actionContentItemOwner extends cmsAction {

    public function run() {

        if(!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) {
            return cmsCore::error404();
        }

        $id = $this->request->get('id', 0);
        if (!$id) {
            return cmsCore::error404();
        }

        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if ($item['user_id'] != $this->cms_user->id || !cmsUser::isAllowed($ctype['name'], 'change_owner')) {
            return cmsCore::error404();
        }

        $form = $this->getForm('change_owner');

        $data = [];

        if ($this->request->has('email')) {

            $ups_key = 'change_owner_' . $ctype['name'] . $item['id'] . '_' . $this->cms_user->id;

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            $user = $this->model_users->getUserByEmail($data['email']);

            if ((!$user || $user['email'] == $this->cms_user->email) && !$errors) {
                $errors['email'] = ERR_USER_NOT_FOUND;
            }

            if (!$errors && cmsUser::getUPS($ups_key, $user['id'])) {
                $errors['email'] = LANG_OFFER_OWNER_SEND_ERROR;
            }

            if ($errors) {

                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }

            $this->controller_messages->addRecipient($user['id']);

            $sender_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';
            $item_link  = '<a href="' . href_to($ctype['name'], $item['slug'] . '.html') . '">' . $item['title'] . '</a>';

            $notice = [
                'content' => sprintf(LANG_OFFER_OWNER_NOTICE, $sender_link, $ctype['labels']['one_genitive'], $item_link),
                'options' => [
                    'is_closeable' => false
                ],
                'actions' => [
                    'accept'  => [
                        'title'      => LANG_ACCEPT,
                        'controller' => $this->name,
                        'action'     => 'item_owner_process',
                        'params'     => [$ctype['name'], $item['id'], $this->cms_user->id, 'accept']
                    ],
                    'decline' => [
                        'title'      => LANG_DECLINE,
                        'controller' => $this->name,
                        'action'     => 'item_owner_process',
                        'params'     => [$ctype['name'], $item['id'], $this->cms_user->id, 'decline']
                    ]
                ]
            ];

            $this->controller_messages->sendNoticePM($notice);

            cmsUser::setUPS($ups_key, 1, $user['id']);

            return $this->cms_template->renderJSON([
                'errors'       => false,
                'success_text' => LANG_OFFER_OWNER_SEND
            ]);
        }

        return $this->cms_template->renderAsset('ui/typical_form', [
            'submit_title' => LANG_CONTINUE,
            'action'       => href_to($ctype['name'], 'owner', [$item['id']]),
            'data'         => $data,
            'form'         => $form,
            'errors'       => $errors ?? false
        ], $this->request);
    }

}
