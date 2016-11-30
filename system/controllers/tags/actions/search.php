<?php

class actionTagsSearch extends cmsAction {

    public function run($ctype_name=false) {

        $query = trim( $this->request->get('q'), ', ' );
        if (!$query) { cmsCore::error404(); }

        $tags = array_map(function($tag) {
            return trim($tag);
        }, explode(',', trim($query)) );

        $tags_ids = $this->model->getTagsIDs($tags);

        $targets = $tags_ids ? $this->model->getTagTargets($tags_ids) : false;

        if (!$targets || !$tags_ids) {
            return $this->cms_template->render('search', array(
                'is_results' => false,
                'tags'       => $query
            ));
        }

        $is_first_tab = !$ctype_name;

        $content_controller = cmsCore::getController('content', $this->request);

        $ctypes = $content_controller->model->getContentTypes();

        foreach ($ctypes as $id => $type) {
            if (!$ctype_name) {
                if (in_array($type['name'], $targets['content'])) {
                    $ctype_name = $type['name'];
                    $ctype = $type;
                    break;
                }
            } else {
                if ($ctype_name == $type['name']) {
                    $ctype = $type;
                    break;
                }
            }
        }

        if (!$ctype) { cmsCore::error404(); }

        $page_url = $is_first_tab ?
                        href_to($this->name, 'search') . "?q={$query}" :
                        href_to($this->name, 'search', array($ctype_name)) . "?q={$query}" ;

        $content_controller->model->filterIn('i.id', $targets['content'][$ctype_name]);
        
        $html = $content_controller->renderItemsList($ctype, $page_url);

        return $this->cms_template->render('search', array(
            'is_results' => true,
            'tags'       => $query,
            'targets'    => $targets,
            'ctypes'     => $ctypes,
            'ctype'      => $ctype,
            'html'       => $html
        ));

    }

}
