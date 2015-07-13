<?php

    $this->setPageTitle(LANG_MODERATION);

    if (!$is_index){
        $this->addBreadcrumb(LANG_MODERATION, $this->href_to(''));
        $this->addBreadcrumb($ctype['title']);
    } else {
        $this->addBreadcrumb(LANG_MODERATION);
    }

    $content_menu = array();

    $is_first = true;

    foreach($counts as $ctype_name=>$count){
        $content_menu[] = array(
            'title' => $ctypes[$ctype_name],
            'url' => $is_first ? $this->href_to('') : $this->href_to('index', $ctype_name),
            'counter' => $count
        );
        $is_first = false;
    }

    $this->addMenuItems('moderation_content_types', $content_menu);

?>

<h1><?php echo LANG_MODERATION; ?></h1>

<div id="moderation_content_pills">
    <?php $this->menu('moderation_content_types', true, 'pills-menu-small'); ?>
</div>

<div id="moderation_content_list"><?php echo $list_html; ?></div>
