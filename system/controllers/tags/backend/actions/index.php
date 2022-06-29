<?php

class actionTagsIndex extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', 'tags');
        $this->setProperty('grid_name', 'tags');
        $this->setProperty('grid_url', $this->cms_template->href_to('index'));
        $this->setProperty('tool_buttons', [
            [
                'class' => 'refresh',
                'title' => LANG_TAGS_RECOUNT,
                'href'  => $this->cms_template->href_to('recount')
            ]
        ]);

    }

}
