<?php

class onContentCommentsTargets extends cmsAction {

    public function run(){

        $types = array();

        $ctypes = $this->model->getContentTypes();

        if ($ctypes) {
            foreach ($ctypes as $ctype) {
                if (!$ctype['options']['list_on']) { continue; }
                $types["content:{$ctype['name']}"] = sprintf(LANG_CP_SETTINGS_FP_SHOW_CONTENT, $ctype['title']);
            }
        }

        return array(
            'name'  => $this->name,
            'types' => $types
        );

    }

}
