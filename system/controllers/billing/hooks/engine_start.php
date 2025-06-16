<?php

class onBillingEngineStart extends cmsAction {

    public function run() {

        if ($this->options['is_refs'] && preg_match('/^r\/([0-9]+)$/i', $this->cms_core->uri, $matches)) {

            $ref_id = $matches[1];

            if (!cmsUser::getCookie('ref_id')) {
                cmsUser::setCookie('ref_id', $ref_id, $this->options['ref_days'] * 60 * 60 * 24);
            }

            $url = $this->options['ref_url'] ? rel_to_href($this->options['ref_url']) : href_to_home();

            return $this->redirect($url);
        }

        return;
    }

}
