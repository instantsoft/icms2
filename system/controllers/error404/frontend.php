<?php

class error404 extends cmsFrontend {

    public function actionIndex() {

        return $this->cms_template->render([]);
    }

}
