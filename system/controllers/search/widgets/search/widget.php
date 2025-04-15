<?php

class widgetSearchSearch extends cmsWidget {

    public function run() {

        $ctype = cmsModel::getCachedResult('current_ctype');

        return [
            'show_input'         => $this->getOption('show_input'),
            'show_search_params' => $this->getOption('show_search_params'),
            'show_btn'           => $this->getOption('show_btn'),
            'action'             => href_to('search', $ctype['name'] ?? ''),
            'query'              => $this->cms_core->request->get('q', ''),
            'type'               => $this->cms_core->request->get('type', 'words'),
            'date'               => $this->cms_core->request->get('date', 'all'),
            'order_by'           => $this->cms_core->request->get('order_by', 'fsort')
        ];
    }

}
