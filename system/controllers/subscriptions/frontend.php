<?php

class subscriptions extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Формирует HTML код для кнопки подписки
     *
     * @param array $target
     */
    public function renderSubscribeButton($target) {

        $hash               = md5(serialize($target));
        $subscribers_count  = 0;
        $user_is_subscribed = false;

        $list_item = $this->model->getSubscriptionItem($hash);

        // если такой список для подписок уже есть
        if($list_item){

            $hash               = $list_item['hash'];
            $subscribers_count  = $list_item['subscribers_count'];
            $user_is_subscribed = $this->isUserSubscribed($list_item['id']);

        }

        return $this->cms_template->renderInternal($this, 'button', array(
            'target'             => $target,
            'hash'               => $hash,
            'subscribers_count'  => $subscribers_count,
            'user_is_subscribed' => (bool)$user_is_subscribed
        ));

    }

    /**
     * Проверяет, подписан ли текущий пользователь на данный список подписки
     *
     * @param integer $list_item_id ID списка подписки
     * @return boolean
     */
    public function isUserSubscribed($list_item_id) {

        if(!$list_item_id) { return false; }

        if($this->cms_user->is_logged){

            return $this->model->isUserSubscribed($this->cms_user->id, $list_item_id);

        } elseif(cmsUser::hasCookie('subscriber_email')) {

            $subscriber_email = cmsUser::getCookie('subscriber_email', 'string', function ($cookie){ return trim($cookie); });

            if($subscriber_email && $this->validate_email($subscriber_email) === true){
                return $this->model->isGuestSubscribed($subscriber_email, $list_item_id);
            }

        }

        return false;

    }

}
