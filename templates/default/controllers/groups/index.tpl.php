<h1>
    <?php echo $h1_title; ?>
    <?php if($dataset_name){ ?>
        <span> / <?php echo $dataset['title']; ?></span>
    <?php } ?>
</h1>

<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => rel_to_href($base_ds_url)
    ));
} ?>

<?php echo $groups_list_html;
