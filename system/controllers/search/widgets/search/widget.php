<?php

class widgetSearchSearch extends cmsWidget {

    public function run() {

        return [
            'query' => cmsCore::getInstance()->request->get('q', '')
        ];
    }

}
