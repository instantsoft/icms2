<?php

class actionAdminCredits extends cmsAction {

    public function run($do = false) {

        $credits_text = file_get_contents(cmsConfig::get('root_path') . 'credits.txt');

        return $this->cms_template->render('credits', [
            'credits_text' => $credits_text
        ]);
    }

}
