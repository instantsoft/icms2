<?php

    $this->setPageTitle($seo_title);
    $this->setPageKeywords($seo_keys);
    $this->setPageDescription($seo_desc);

    $this->addBreadcrumb($seo_title);

    $this->addTplJSNameFromContext('vendors/slick/slick.min');
    $this->addTplCSSNameFromContext('slick');
?>

<h1><?php echo $seo_h1; ?></h1>

<div id="tags_search_pills">
    <?php $this->menu('results_tabs', true, 'nav nav-pills tags-search__pills mb-3 mb-md-4'); ?>
</div>

<?php echo $html; ?>
<?php ob_start(); ?>
<script>
    icms.menu.initSwipe('.tags-search__pills', {initialSlide: $('.tags-search__pills > li.is-active').index()});
</script>
<?php $this->addBottom(ob_get_clean()); ?>