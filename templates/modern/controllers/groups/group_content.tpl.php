<?php $this->renderChild('group_header', array('group' => $group, 'filter_titles' => $filter_titles)); ?>
<div class="icms-body-toolbox row align-items-center mt-3 mt-md-4">
    <div class="col-sm">
    <?php if (!empty($datasets)){ ?>
        <?php $this->renderAsset('ui/datasets-panel', array(
            'wrap_class'      => 'flex-fill',
            'datasets'        => $datasets,
            'dataset_name'    => $dataset,
            'current_dataset' => $current_dataset,
            'base_ds_url'     => $base_ds_url
        )); ?>
    <?php } ?>
    </div>
<?php if ($toolbar_html) { ?>
    <div class="col-sm-auto ml-n2">
        <div class="mt-3 mt-sm-0"><?php echo html_each($toolbar_html); ?></div>
    </div>
<?php } ?>
</div>
<div id="group_content_list">
    <?php echo $html; ?>
</div>