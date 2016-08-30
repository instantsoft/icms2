<?php

class actionTagsSearch extends cmsAction {

    public function run($ctype_name=false){

        $query = $this->request->get('q', '');
        if (!$query) { cmsCore::error404(); }

        $tag_id = $this->model->getTagId($query);

        $targets = $tag_id ? $this->model->getTagTargets($tag_id) : false;

        if (!$targets || !$tag_id) {
            return $this->cms_template->render('search', array(
                'is_results' => false,
                'tag'        => $query
            ));
        }

        $is_first_tab = !$ctype_name;

        $content_controller = cmsCore::getController('content', $this->request);

        $ctypes = $content_controller->model->getContentTypes();

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

        $page_url = $is_first_tab ?
                        href_to($this->name, 'search') . "?q={$query}" :
                        href_to($this->name, 'search', array($ctype_name)) . "?q={$query}" ;

        $html = $content_controller->renderItemsList($ctype, $page_url);

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
