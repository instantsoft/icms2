<?php
    $this->addBreadcrumb(LANG_ACTIVITY);
?>
<h1>
    <?php $this->pageH1(); ?>
</h1>

<?php if (count($datasets) > 1){
    $this->renderAsset('ui/datasets-panel', [
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ]);
} ?>

<?php echo $items_list_html;
