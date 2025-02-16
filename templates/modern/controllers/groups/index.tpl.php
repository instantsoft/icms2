<h1>
    <?php $this->pageH1();?>
</h1>

<?php if (!empty($datasets)) {
    $this->renderAsset('ui/datasets-panel', [
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => rel_to_href($base_ds_url)
    ]);
} ?>

<?php echo $groups_list_html;
