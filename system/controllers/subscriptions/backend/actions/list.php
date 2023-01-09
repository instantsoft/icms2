<?php

class actionSubscriptionsList extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'subscriptions';
        $this->grid_name  = 'subscriptions';
        $this->title      = LANG_SBSCR_LIST;

        $this->tool_buttons = [
            [
                'class'   => 'delete',
                'title'   => LANG_DELETE,
                'href'    => null,
                'onclick' => "return icms.datagrid.submit('".$this->cms_template->href_to('delete').'?csrf_token='.cmsForm::getCSRFToken()."', '".LANG_DELETE_SELECTED_CONFIRM."')"
            ]
        ];

        $this->items_callback = function($items){

            if($items){

                foreach ($items as $key => $item) {

                    $items[$key]['params'] = cmsModel::stringToArray($item['params']);
                }
            }

            $items = cmsEventsManager::hook('admin_subscriptions_list', $items);

            return $items;
        };
    }

}
