<?php

class onContentSitemapSources extends cmsAction {

    public function run() {

        $ctypes = $this->model->getContentTypes();

        $sources = array();

        foreach ($ctypes as $ctype) {

            if(!empty($ctype['labels']['many'])){

                $datasets = $this->getCtypeDatasets($ctype, array(
                    'cat_id' => false
                ));

                if (empty($ctype['options']['list_off_index']) || $ctype['is_cats'] || ($datasets && count($datasets) > 1)) {
                    $sources[$ctype['name'].'|cats'] = LANG_CONTENT_CONTROLLER . ': ' . LANG_CATEGORIES . ' ' . $ctype['labels']['many'];
                }
            }

            $sources[$ctype['name']] = LANG_CONTENT_CONTROLLER . ': ' . $ctype['title'];

        }

        return array(
            'name'    => $this->name,
            'sources' => $sources
        );

    }

}
