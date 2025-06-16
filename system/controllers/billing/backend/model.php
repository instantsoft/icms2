<?php
/**
 * Класс модели, предназначенный для бэкенда
 */
class modelBackendBilling extends modelBilling {

    public function updatePaymentSystem($id, $system) {
        return $this->update('billing_systems', $id, $system);
    }

    public function updatePricesList() {

        $content_model = cmsCore::getModel('content');

        $actions = $this->getActions();

        if (empty($actions['content'])) {
            $actions['content'] = [];
        }

        $content_actions = array_collection_to_list($actions['content'], 'name');

        $ctypes = $content_model->getContentTypes();

        foreach ($ctypes as $ctype) {
            if (!in_array("{$ctype['name']}_add", $content_actions)) {
                $this->addAction([
                    'controller' => 'content',
                    'name'       => "{$ctype['name']}_add",
                    'title'      => sprintf(LANG_BILLING_ACTION_ADD_CONTENT, $ctype['title'])
                ]);
            }
        }

        return true;
    }

    public function getProfitStats() {

        $stats = [];

        //today
        $result = $this->
                selectOnly('SUM(summ)', 'total')->
                filterEqual('status', parent::STATUS_DONE)->
                filterGt('summ', 0)->
                filter('DATEDIFF(NOW(), i.date_done) < 1')->
                get('billing_log', false, false);

        $stats[] = $result[0]['total'] ?? 0;

        //yesterday
        $result = $this->
                selectOnly('SUM(summ)', 'total')->
                filterEqual('status', parent::STATUS_DONE)->
                filterGt('summ', 0)->
                filter('DATE(i.date_done) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))')->
                get('billing_log', false, false);

        $stats[] = $result[0]['total'] ?? 0;

        //week
        $result = $this->
                selectOnly('SUM(summ)', 'total')->
                filterEqual('status', parent::STATUS_DONE)->
                filterGt('summ', 0)->
                filterDateYounger('date_done', 1, 'WEEK')->
                get('billing_log', false, false);

        $stats[] = $result[0]['total'] ?? 0;

        //month
        $result = $this->
                selectOnly('SUM(summ)', 'total')->
                filterEqual('status', parent::STATUS_DONE)->
                filterGt('summ', 0)->
                filterDateYounger('date_done', 1, 'MONTH')->
                get('billing_log', false, false);

        $stats[] = $result[0]['total'] ?? 0;

        return $stats;
    }

    public function getTopBalanceUsers($order_to = 'desc', $limit = 10) {

        $this->joinSessionsOnline('i');

        return $this->orderBy('balance', $order_to)->limit($limit)->get('{users}', function($user, $model) {

            $user['slug'] = !empty($user['slug']) ? $user['slug'] : $user['id'];

            return $user;
        });
    }

    public function getTotalBalance() {

        $result = $this->limit(false)->
                selectOnly('SUM(balance)', 'total')->
                get('{users}', false, false);

        return $result[0]['total'] ?? 0;
    }

    public function getPendingOutsCount() {
        return $this->filterEqual('status', parent::OUT_STATUS_CONFIRMED)->
                getCount('billing_outs', 'id', true);
    }

}
