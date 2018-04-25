<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group' => $group, 'filter_titles' => $filter_titles)); ?>
</div>
<?php
    if ($toolbar_html) {
        echo html_each($toolbar_html);
    }
?>
<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>
<div id="group_content_list">
    <?php echo $html; ?>
</div>