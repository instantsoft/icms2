<?php $this->renderChild('group_header', array('group' => $group, 'filter_titles' => $filter_titles)); ?>
<div class="d-flex align-items-center">
<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'wrap_class'      => 'flex-fill',
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>
<?php
    if ($toolbar_html) {
        echo html_each($toolbar_html);
    }
?>
</div>
<div id="group_content_list">
    <?php echo $html; ?>
</div>