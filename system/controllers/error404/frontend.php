<?php

class error404 extends cmsFrontend {

    public function actionIndex() {

        $css_file = $this->cms_template->getStylesFileName($this->cms_core->uri_controller);
        if ($css_file) { $this->cms_template->addCSS($css_file); }

        return $this->cms_template->render([]);
    }

}
