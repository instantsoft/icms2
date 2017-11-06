<?php
class cmsWysiwygMarkitup{

	public function displayEditor($field_id, $content=''){

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_editor($field_id, $content, array('id'=>$dom_id));

	}

}