<?php
    $this->setPageTitle($page_title);
    $this->addBreadcrumb(LANG_COMMENTS);
?>
<?php if ($rss_link){ ?>
    <div class="content_list_rss_icon">
        <a href="<?php echo $rss_link; ?>">RSS</a>
    </div>
<?php } ?>
<h1><?php echo LANG_COMMENTS; ?></h1>

<?php if (count($datasets) > 1){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>

<?php echo $items_list_html;
