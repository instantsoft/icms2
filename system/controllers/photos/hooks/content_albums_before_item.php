<?php

class onPhotosContentAlbumsBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $album, $fields) = $data;

        $is_allow = $album['is_public'] || ($album['user_id'] == $this->cms_user->id) || $this->cms_user->is_admin;

        if ($is_allow && cmsUser::isAllowed($ctype['name'], 'add')) {

            $this->cms_template->addToolButton(array(
                'class' => 'images',
                'title' => LANG_PHOTOS_UPLOAD,
                'href'  => href_to($this->name, 'upload', $album['id'])
            ));

        }

        $album['filter_panel'] = array(
            'ordering'    => modelPhotos::getOrderList(),
            'types'       => (!empty($this->options['types']) ? (array('' => LANG_PHOTOS_ALL) + $this->options['types']) : array()),
            'orientation' => modelPhotos::getOrientationList(),
            'width'       => '',
            'height'      => ''
        );

        $album['filter_values'] = array(
            'ordering'    => $this->cms_core->request->get('ordering', $this->options['ordering']),
            'types'       => $this->cms_core->request->get('types', ''),
            'orientation' => $this->cms_core->request->get('orientation', ''),
            'width'       => $this->cms_core->request->get('width', 0) ?: '',
            'height'      => $this->cms_core->request->get('height', 0) ?: ''
        );

        $album['url_params'] = array_filter($album['filter_values']);

        $album['filter_selected'] = $album['url_params'];
        if($album['filter_selected']['ordering'] == $this->options['ordering']){ unset($album['filter_selected']['ordering']); }

        if(!in_array($album['filter_values']['ordering'], array_keys($album['filter_panel']['ordering']))){
            $album['filter_values']['ordering'] = 'date_pub';
        }

        if($album['filter_values']['types'] && !in_array($album['filter_values']['types'], array_keys($album['filter_panel']['types']))){
            $album['filter_values']['types'] = '';
        }

        if($album['filter_values']['orientation'] && !in_array($album['filter_values']['orientation'], array_keys($album['filter_panel']['orientation']))){
            $album['filter_values']['orientation'] = '';
        }

        $album['base_url'] = href_to($ctype['name'], $album['slug'].'.html').'?'.http_build_query($album['url_params']);

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
