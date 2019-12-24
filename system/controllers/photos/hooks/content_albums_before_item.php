<?php

class onPhotosContentAlbumsBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $album, $fields) = $data;

        $is_allow = $album['is_public'] || !$album['id'] || ($album['user_id'] == $this->cms_user->id) || $this->cms_user->is_admin;

        if ($is_allow && cmsUser::isAllowed($ctype['name'], 'add')) {

            $this->cms_template->addToolButton(array(
                'class' => 'images',
                'title' => LANG_PHOTOS_UPLOAD,
                'href'  => href_to($this->name, 'upload', $album['id'])
            ));

        }

        $album['filter_panel'] = array(
            'ordering'    => modelPhotos::getOrderList(),
            'orderto'     => array('asc' => LANG_SORTING_ASC, 'desc' => LANG_SORTING_DESC),
            'type'        => (!empty($this->options['types']) ? (array('' => LANG_PHOTOS_ALL) + $this->options['types']) : array()),
            'orientation' => modelPhotos::getOrientationList(),
            'width'       => '',
            'height'      => ''
        );

        $album['filter_values'] = array(
            'ordering'    => $this->cms_core->request->get('ordering', $this->options['ordering']),
            'orderto'     => $this->cms_core->request->get('orderto', $this->options['orderto']),
            'type'        => $this->cms_core->request->get('type', ''),
            'orientation' => $this->cms_core->request->get('orientation', ''),
            'width'       => $this->cms_core->request->get('width', 0) ?: '',
            'height'      => $this->cms_core->request->get('height', 0) ?: ''
        );

        $album['url_params'] = array_filter($album['filter_values']);

        $album['filter_selected'] = $album['url_params'];
        if($album['filter_selected']['ordering'] == $this->options['ordering']){ unset($album['filter_selected']['ordering']); }
        if($album['filter_selected']['orderto'] == $this->options['orderto']){ unset($album['filter_selected']['orderto']); }

        $album['photos_url_params'] = array();
        if(!empty($album['filter_selected']['ordering'])){
            $album['photos_url_params']['ordering'] = $album['filter_selected']['ordering'];
        }
        if(!empty($album['filter_selected']['orderto'])){
            $album['photos_url_params']['orderto'] = $album['filter_selected']['orderto'];
        }
        if($album['photos_url_params']){
            $album['photos_url_params'] = http_build_query($album['photos_url_params']);
        }

        if(!in_array($album['filter_values']['ordering'], array_keys($album['filter_panel']['ordering']))){
            $album['filter_values']['ordering'] = 'date_pub';
        }

        if(!in_array($album['filter_values']['orderto'], array('asc', 'desc'))){
            $album['filter_values']['orderto'] = 'desc';
        }

        if($album['filter_values']['type'] && !isset($album['filter_panel']['type'][$album['filter_values']['type']])){
            $album['filter_values']['type'] = '';
        }

        if($album['filter_values']['orientation'] && !in_array($album['filter_values']['orientation'], array_keys($album['filter_panel']['orientation']))){
            $album['filter_values']['orientation'] = '';
        }

        if(!empty($album['slug'])){
            $album['base_url'] = href_to($ctype['name'], $album['slug'].'.html').'?'.http_build_query($album['url_params']);
        } else {
            $album['base_url'] = href_to('photos').'?'.http_build_query($album['url_params']);
        }

        foreach ($album['filter_selected'] as $key => $value) {
            if(isset($album['filter_panel'][$key][$value])){
                $title[] = $album['filter_panel'][$key][$value];
            }
        }
        if(!empty($title)){
            $album['title'] .= ' — '.mb_strtolower(implode(', ', $title));
            $album['seo_desc'] .= ' '.$album['title'];
        }

        return array($ctype, $album, $fields);

    }

}
