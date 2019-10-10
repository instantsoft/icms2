<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addTplJSName([
        'jquery-cookie',
        'admin-chart',
        'admin-dashboard'
        ]);
?>
<h1>
    <div class="btn-group float-right" role="group">
        <a class="btn ajax-modal text-muted" href="<?php echo $this->href_to('index_page_settings'); ?>" title="<?php echo LANG_CONFIG; ?>">
            <i class="icon-settings"></i> <?php echo LANG_CP_PAGE_OPTIONS; ?>
        </a>
    </div>
    <?php echo LANG_ADMIN_CONTROLLER; ?>
</h1>

<div id="dashboard" class="card-columns cols-2" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $dashboard_block) { ?>
    <div class="card <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : ''); ?>" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
        <div class="card-header">
            <?php echo $dashboard_block['title']; ?> <span class="db_spinner sk-spinner sk-spinner-pulse bg-blue ml-3" style="display: none"></span>
            <div class="card-header-actions actions-toolbar">
                <i class="icon-cursor-move icons font-2xl"></i>
            </div>
        </div>
        <div class="card-body"><?php echo $dashboard_block['html']; ?></div>
    </div>
<?php } ?>
</div>