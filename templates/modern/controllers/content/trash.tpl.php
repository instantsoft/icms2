<?php

    $this->setPageTitle(LANG_BASKET_TITLE);

    if (!$is_index){
        $this->addBreadcrumb(LANG_BASKET_TITLE, $this->href_to('trash'));
        $this->addBreadcrumb($ctype['title']);
    } else {
        $this->addBreadcrumb(LANG_BASKET_TITLE);
    }

    $content_menu = array(); $is_first = true;

    foreach($ctypes as $ctype_name => $ctype){
        $content_menu[] = array(
            'title'   => $ctype['title'],
            'url'     => $is_first ? $this->href_to('trash') : $this->href_to('trash', $ctype_name),
            'counter' => $counts[$ctype_name]
        );
        $is_first = false;
    }

    $this->addMenuItems('trash_content_types', $content_menu);

?>

<h1><?php echo LANG_BASKET_TITLE; ?></h1>

<div id="trash_content_pills">
    <?php $this->menu('trash_content_types', true, 'nav nav-pills tags-search__pills mb-3 mb-md-4'); ?>
</div>

<div id="trash_content_list"><?php echo $list_html; ?></div>