<?php

class onContentSitemapSources extends cmsAction {

    public function run() {

        $ctypes = $this->model->getContentTypes();

        $sources = array();

        foreach ($ctypes as $ctype) {

            if(!empty($ctype['labels']['many']) && !empty($ctype['is_cats'])){
                $sources[$ctype['name'].'|cats'] = LANG_CONTENT_CONTROLLER . ': ' . LANG_CATEGORIES . ' ' . $ctype['labels']['many'];
            }

            $sources[$ctype['name']] = LANG_CONTENT_CONTROLLER . ': ' . $ctype['title'];

        }

        return array(
            'name'    => $this->name,
            'sources' => $sources
        );

    }

}
