<?php

    $this->setPageTitle($seo_title);
    $this->setPageKeywords($seo_keys);
    $this->setPageDescription($seo_desc);

    $this->addBreadcrumb($seo_title);
?>

<h1><?php echo $seo_h1; ?></h1>

<?php if (!empty($tag['description'])) { ?>
<div class="tags-search__description my-3">
    <?php echo $tag['description']; ?>
</div>
<?php } ?>

<div id="tags_search_pills" class="mobile-menu-wrapper mb-3 mb-md-4">
    <?php $this->menu('results_tabs', true, 'nav nav-pills tags-search__pills'); ?>
</div>

<?php echo $html; ?>