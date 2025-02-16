<?php
    $this->addBreadcrumb(LANG_USERS);
?>

<h1>
    <?php $this->pageH1(); ?>
    <?php if($dataset_name){ ?>
        <span> / <?php echo $dataset['title']; ?></span>
    <?php } ?>
</h1>

<?php if (count($datasets) > 1){
    $this->renderAsset('ui/datasets-panel', [
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ]);
} ?>

<?php echo $profiles_list_html;
