<?php

class onPhotosContentAlbumsFormHtml extends cmsAction {

    public function run($data){

        $do = $data['do'];
        $id = $data['id'];

        if ($do == 'edit'){
            $photos = $this->model->getPhotos($id);
        }

        $template = cmsTemplate::getInstance();

        return $template->renderInternal($this, 'widget', array(
            'do' => $do,
            'id' => $id,
            'photos' => isset($photos) ? $photos : false
        ));

    }

}
