<?php

class onContentCtypeRelationChilds extends cmsAction {

    public function run($ctype_id){

        $types = array();

        $ctypes = $this->model->getContentTypes();

        if ($ctypes) {
            foreach ($ctypes as $ctype) {
                if ($ctype['id'] == $ctype_id) { continue; }
                $types["content:{$ctype['id']}"] = sprintf(LANG_CP_SETTINGS_FP_SHOW_CONTENT, $ctype['title']);
            }
        }

        return array(
            'name'  => $this->name,
            'types' => $types
        );

    }

}
