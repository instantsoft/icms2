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

        $tag = $this->model->getTagByTag($query);

        $targets = !empty($tag['id']) ? $this->model->getTagTargets($tag['id']) : false;

        if (!$targets || empty($tag['id'])) {

            // Возвращаем код 404 если тег не найден
            if(ob_get_length()) { ob_end_clean(); }
            header("HTTP/1.0 404 Not Found");
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");

            return $this->cms_template->render('search', array(
                'is_results' => false,
                'tag'        => $query,
                'seo_title'  => '',
                'seo_keys'   => '',
                'seo_desc'   => '',
                'seo_h1'     => ''
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
                filterEqual('t.tag_id', $tag['id']);

        $page_url = array(
            'base'   => href_to($this->name, 'search', array($ctype_name)),
            'first'  => href_to($this->name, 'search', array($ctype_name)),
            'cancel' => href_to($this->name, 'search', array($ctype_name)).'?q='.urlencode($query)
        );

        $html = $content_controller->setListContext('search')->renderItemsList($ctype, $page_url, false, 0, array(), false, array(
            'q' => $query
        ));

        $seo_title = sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag['tag']);
        $seo_keys  = $seo_title;
        $seo_desc  = $seo_title;
        $seo_h1    = $seo_title;

        if(!empty($ctype['seo_keys'])){
            $seo_keys = $ctype['seo_keys'];
        }

        if(!empty($ctype['seo_desc'])){
            $seo_desc = $ctype['seo_desc'];
        }

        $seo_data = array(
            'tag'         => $tag['tag'],
            'ctype_title' => $ctype['title']
        );

        if($tag['tag_title']){
            $seo_title = string_replace_keys_values($tag['tag_title'], $seo_data);
        }

        if($tag['tag_desc']){
            $seo_desc = string_replace_keys_values($tag['tag_desc'], $seo_data);
        }

        if($tag['tag_h1']){
            $seo_h1 = string_replace_keys_values($tag['tag_h1'], $seo_data);
        }

        foreach($ctypes as $type){
            if (!in_array($type['name'], $targets['content'])) { continue; }
            $content_menu[] = array(
                'title'    => $type['title'],
                'url'      => href_to('tags', 'search', array($type['name'])).'?q='.$tag['tag'],
                'url_mask' => href_to('tags', 'search', array($type['name']))
            );
        }

        $content_menu[0]['url']      = href_to('tags', 'search').'?q='.$tag['tag'];
        $content_menu[0]['url_mask'] = href_to('tags', 'search');

        $this->cms_template->addMenuItems('results_tabs', $content_menu);

        if ($this->cms_user->is_admin){
            $this->cms_template->addToolButton(array(
                'class' => 'page_gear',
                'title' => LANG_TAGS_SETTINGS,
                'href'  => href_to('admin', 'controllers', array('edit', 'tags'))
            ));
        }

        return $this->cms_template->render('search', array(
            'is_results' => true,
            'tag'        => $query,
            'targets'    => $targets,
            'ctypes'     => $ctypes,
            'ctype'      => $ctype,
            'seo_title'  => $seo_title,
            'seo_keys'   => $seo_keys,
            'seo_desc'   => $seo_desc,
            'seo_h1'     => $seo_h1,
            'html'       => $html
        ));

    }

}
