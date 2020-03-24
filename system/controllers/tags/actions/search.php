<?php
/**
 * Редирект 301 со старых адресов
 * @todo убрать через пару релизов
 */
class actionTagsSearch extends cmsAction {

    public function run($ctype_name = false){

        $query = $this->request->get('q', '');
        if (!$query) {
            cmsCore::error404();
        }

        if($ctype_name){
            $this->redirect(href_to('tags', 'content-'.$ctype_name, urlencode($query)), 301);
        }

        $this->redirect(href_to('tags', urlencode($query)), 301);

    }

}
