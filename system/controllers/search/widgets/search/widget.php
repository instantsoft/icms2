<?php

class widgetSearchSearch extends cmsWidget {

    public function run() {

        $ctype = cmsModel::getCachedResult('current_ctype');

        return [
            'show_btn' => $this->getOption('show_btn'),
            'action'   => href_to('search', $ctype['name'] ?? ''),
            'query'    => cmsCore::getInstance()->request->get('q', '')
        ];
    }

}
