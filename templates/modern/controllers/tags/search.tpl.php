<?php

    $this->setPageTitle($seo_title);
    $this->setPageKeywords($seo_keys);
    $this->setPageDescription($seo_desc);

    $this->addBreadcrumb($seo_title);

    $this->addHead('<link rel="canonical" href="' . href_to_abs('tags', $target, string_urlencode($tag['tag'])) . ($page > 1 ? '?page='.$page : '') . '">');
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
