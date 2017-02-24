<?php

class actionTagsSearch extends cmsAction {

    public function run($ctype_name = false){

        $content_controller = cmsCore::getController('content', $this->request);

        $ctypes = $content_controller->model->getContentTypes();

        $query = $this->request->get('q', '');
        if (!$query) {
            if($ctype_name && $content_controller->model->getContentTypeByName($ctype_name)){
                $this->redirectTo($ctype_name);
            } else {
                cmsCore::error404();
            }
        }

        $tag_id = $this->model->getTagId($query);

        $targets = $tag_id ? $this->model->getTagTargets($tag_id) : false;

        if (!$targets || !$tag_id) {
            return $this->cms_template->render('search', array(
                'is_results' => false,
                'tag'        => $query
            ));
        }

        foreach($ctypes as $id => $type){
            if (!$ctype_name){
                if (in_array($type['name'], $targets['content'])){
                    $ctype_name = $type['name'];
                    $ctype = $type;
                    break;
                }
            } else {
                if ($ctype_name == $type['name']){
                    $ctype = $type;
                    break;
                }
            }
        }

        if (!$ctype) { cmsCore::error404(); }

        $content_controller->model->
                join('tags_bind', 't', "t.target_id = i.id AND t.target_subject = '{$ctype_name}' AND t.target_controller = 'content'")->
                filterEqual('t.tag_id', $tag_id);

        $page_url = array(
            'base'   => href_to($this->name, 'search', array($ctype_name)),
            'first'  => href_to($this->name, 'search', array($ctype_name)),
            'cancel' => href_to($this->name, 'search', array($ctype_name)).'?q='.urlencode($query)
        );

        $html = $content_controller->renderItemsList($ctype, $page_url, false, 0, array(), false, array(
            'q' => $query
        ));

        return $this->cms_template->render('search', array(
            'is_results' => true,
            'tag'        => $query,
            'targets'    => $targets,
            'ctypes'     => $ctypes,
            'ctype'      => $ctype,
            'html'       => $html
        ));

    }

}
