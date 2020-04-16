<?php

    $list_header = empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'];

    $this->setPageTitle($list_header, $profile['nickname']);

    $this->addBreadcrumb($list_header);

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $is_allowed = true;

        if ($is_allowed){

            $href = href_to($ctype['name'], 'add');

            $this->addToolButton(array(
                'class' => 'add',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href,
            ));

        }

    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }

?>

<div id="user_content_list"><?php echo $html; ?></div>