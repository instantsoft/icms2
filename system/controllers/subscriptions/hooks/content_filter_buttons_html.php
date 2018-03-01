<?php

class onSubscriptionsContentFilterButtonsHtml extends cmsAction {

    public function run($data){

        list($ctype_name, $form_url, $filters) = $data;

        return $this->renderSubscribeButton(array(
            'controller' => 'content',
            'subject'    => $ctype_name,
            'params'     => $filters
        ));

    }

}
