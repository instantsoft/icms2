<?php
class cmsWysiwygMarkitup {

    public function displayEditor($field_id, $content = '', $config = array()) {

        $dom_id = str_replace(array('[', ']'), array('_', ''), $field_id);

        echo html_editor($field_id, $content, array_merge($config, array('id' => $dom_id)));

    }

}
