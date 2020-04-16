<?php
class backendGeo extends cmsBackend {

    protected $useOptions = true;

    public $useDefaultOptionsAction = true;

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_GEO_CONTROLLER,
                'url'   => href_to($this->root_url)
            ),
            array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            )
        );
    }

}
