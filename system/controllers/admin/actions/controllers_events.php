<?php

class actionAdminControllersEvents extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'events';
        $this->grid_name  = 'controllers_events';

        $this->list_callback = function ($model) {

            $model->limit(false);

            return $model;
        };
    }

    public function getListItemsGridHtml(){

        $diff_events = $this->getEventsDifferences();

        if (!empty($diff_events['added']) || !empty($diff_events['deleted'])) {

            $this->tool_buttons[] = [
                'class' => 'refresh',
                'title' => LANG_EVENTS_REFRESH,
                'href'  => $this->cms_template->href_to('controllers', ['events_update'])
            ];
        }

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'controllers_events', [
            'events_add'    => $diff_events['added'],
            'events_delete' => $diff_events['deleted'],
            'grid_html'     => $grid_html
        ]);
    }

}
