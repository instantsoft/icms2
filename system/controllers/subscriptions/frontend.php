<?php
/**
 * @property \modelSubscriptions $model
 */
class subscriptions extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Формирует HTML код для кнопки подписки
     *
     * @param array $target Массив данных для кнопки
     * @param boolean $show_btn_title Показывать надписи подписаться/отписаться
     */
    public function renderSubscribeButton($target, $show_btn_title = null) {

        if($show_btn_title === null) {

            $show_btn_title = true;

            if(array_key_exists('show_btn_title', $this->options)){
                $show_btn_title = $this->options['show_btn_title'];
            }
        }

        // убираем пустые массивы
        if (empty($target['params']['field_filters'])) {
            unset($target['params']['field_filters']);
        }
        if (empty($target['params']['filters'])) {
            unset($target['params']['filters']);
        }
        if (empty($target['params']['dataset'])) {
            unset($target['params']['dataset']);
        }

        $hash               = md5(serialize($target));
        $subscribers_count  = 0;
        $user_is_subscribed = false;

        $list_item = $this->model->getSubscriptionItem($hash);

        // если такой список для подписок уже есть
        if ($list_item) {

            $hash               = $list_item['hash'];
            $subscribers_count  = $list_item['subscribers_count'];
            $user_is_subscribed = $this->isUserSubscribed($list_item['id']);
        }

        return $this->cms_template->renderInternal($this, 'button', [
            'show_btn_title'     => $show_btn_title,
            'target'             => $target,
            'hash'               => $hash,
            'subscribers_count'  => $subscribers_count,
            'user_is_subscribed' => (bool) $user_is_subscribed
        ]);
    }

    /**
     * Проверяет, подписан ли текущий пользователь на данный список подписки
     *
     * @param integer $list_item_id ID списка подписки
     * @return boolean
     */
    public function isUserSubscribed($list_item_id) {

        if (!$list_item_id) {
            return false;
        }

        if ($this->cms_user->is_logged) {

            return $this->model->isUserSubscribed($this->cms_user->id, $list_item_id);

        } elseif (cmsUser::hasCookie('subscriber_email')) {

            $subscriber_email = cmsUser::getCookie('subscriber_email', 'string', function ($cookie) {
                return trim($cookie);
            });

            if ($subscriber_email && $this->validate_email($subscriber_email) === true) {
                return $this->model->isGuestSubscribed($subscriber_email, $list_item_id);
            }
        }

        return false;
    }

    /**
     * Формирует список подписок
     *
     * @param string $base_url URL списка
     * @param integer $page Номер страницы
     * @param integer $perpage Кол-во на страницу
     * @return string
     */
    public function renderSubscriptionsList($base_url, $page, $perpage = false) {

        $perpage = ($perpage ? $perpage : $this->options['limit']);

        if (!$this->model->order_by) {
            $this->model->orderBy('i.date_pub', 'desc');
        }

        // получаем на одну страницу больше
        $this->model->limitPagePlus($page, $perpage);

        $items = $this->model->getSubscriptions();
        if (!$items && $page > 1) {
            return false;
        }

        if ($items && (count($items) > $perpage)) {
            $has_next = true;
            array_pop($items);
        } else {
            $has_next = false;
        }

        $items = cmsEventsManager::hook('subscriptions_list', $items);

        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        list($fields, $this->model_users) = cmsEventsManager::hook('profiles_list_filter', [$fields, $this->model_users]);

        $html = $this->cms_template->renderInternal($this, 'list', [
            'user'     => $this->cms_user,
            'fields'   => $fields,
            'is_ajax'  => $this->request->isAjax(),
            'items'    => $items,
            'base_url' => $base_url,
            'page'     => $page,
            'has_next' => $has_next
        ]);

        if (!$this->request->isAjax()) {

            return $html;

        } else {

            return $this->cms_template->renderJSON([
                'html'     => $html,
                'has_next' => $has_next,
                'page'     => $page
            ]);
        }
    }

}
