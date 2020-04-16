<?php
    $this->setPageTitle($page_title);
    $this->addBreadcrumb(LANG_USERS);
?>

<h1>
    <?php echo LANG_USERS; ?>
    <?php if($dataset_name != 'all'){ ?>
    <span> / <?php echo $dataset['title']; ?></span>
    <?php } ?>
</h1>

<?php if (count($datasets) > 1){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>

<?php echo $profiles_list_html;
