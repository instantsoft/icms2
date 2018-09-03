<?php

    $this->setPageTitle($seo_title);
    $this->setPageKeywords($seo_keys);
    $this->setPageDescription($seo_desc);

    $this->addBreadcrumb($seo_title);

?>

<h1><?php echo $seo_h1; ?></h1>

<div id="tags_search_pills">
    <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
</div>

<div id="tags_search_list"><?php echo $html; ?></div>