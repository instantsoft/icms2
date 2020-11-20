<?php $this->renderChild('group_header', array('group' => $group, 'filter_titles' => $filter_titles)); ?>
<div class="row align-items-center">
<?php if (!empty($datasets)){ ?>
    <div class="col-sm">
        <?php $this->renderAsset('ui/datasets-panel', array(
            'wrap_class'      => 'flex-fill',
            'datasets'        => $datasets,
            'dataset_name'    => $dataset,
            'current_dataset' => $current_dataset,
            'base_ds_url'     => $base_ds_url
        )); ?>
    </div>
<?php } ?>
<?php if ($toolbar_html) { ?>
    <div class="col-sm-auto ml-n2">
        <?php echo html_each($toolbar_html); ?>
    </div>
<?php } ?>
</div>
<div id="group_content_list">
    <?php echo $html; ?>
</div>