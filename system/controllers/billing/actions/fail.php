<?php

class actionBillingFail extends cmsAction {

    use \icms\controllers\billing\traits\validatepay;

    public function run() {

        return $this->cms_template->render([
            'next_url' => href_to_home()
        ]);
    }

}
