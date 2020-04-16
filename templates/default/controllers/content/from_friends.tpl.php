<?php

    $list_header = sprintf(LANG_CONTENT_PRIVATE_FRIEND_ITEMS, mb_strtolower($ctype['title']));

    $this->setPageTitle($list_header);

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    $this->addBreadcrumb($list_header);

?>

<h1><?php echo $list_header; ?></h1>

<?php echo $items_list_html; ?>
