<?php

class onContentCtypeRelationChilds extends cmsAction {

    public function run($ctype_id) {

        $types = [];

        $ctypes = $this->model->getContentTypesFiltered();

        if ($ctypes) {
            foreach ($ctypes as $ctype) {
                if ($ctype['id'] == $ctype_id) {
                    continue;
                }
                $types["content:{$ctype['id']}"] = sprintf(LANG_CP_SETTINGS_FP_SHOW_CONTENT, $ctype['title']);
            }
        }

        return [
            'name'  => $this->name,
            'types' => $types
        ];
    }

}
