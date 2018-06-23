<?php

class actionSubscriptionsListSubscribers extends cmsAction {

    public function run($hash){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $subscription = $this->model->getSubscriptionItem($hash);
        if(!$subscription){ return cmsCore::error404(); }

        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        $page = $this->request->get('page', 1);

        $this->model_users->joinRight('subscriptions_bind', 'sb', 'sb.user_id = i.id');
        $this->model_users->filterEqual('sb.subscription_id', $subscription['id']);
        $this->model_users->filterEqual('sb.is_confirmed', 1);
        $this->model_users->select('sb.guest_name');
        $this->model_users->select('sb.date_pub', 'date_subscribe');

        $this->model_users->limitPagePlus($page, $this->options['limit']);

        $this->model_users->orderBy('i.date_log', 'desc');

        list($fields, $this->model_users) = cmsEventsManager::hook('profiles_list_filter', array($fields, $this->model_users));

        $profiles = $this->model_users->getUsers();

        if(!$profiles && $page > 1){ return false; }

        if($profiles && (count($profiles) > $this->options['limit'])){
            $has_next = true; array_pop($profiles);
        } else {
            $has_next = false;
        }

        $html = $this->cms_template->renderInternal($this, 'list_subscribers', array(
            'user'     => $this->cms_user,
            'profiles' => $profiles,
            'fields'   => $fields,
            'base_url' => href_to($this->name, 'list_subscribers', array($hash)),
            'page'     => $page,
            'has_next' => $has_next
        ));

        return $this->cms_template->renderJSON(array(
            'html'     => $html,
            'has_next' => $has_next,
            'page'     => $page
        ));

    }

}
