<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addTplJSName([
        'jquery-cookie',
        'admin-chart',
        'admin-dashboard'
        ]);
?>
<div class="page_options_block">
    <a class="ajax-modal ajaxlink" href="<?php echo $this->href_to('index_page_settings'); ?>" title="<?php echo LANG_CONFIG; ?>">
        <?php echo LANG_CP_PAGE_OPTIONS; ?>
    </a>
</div>
<h1><?php echo LANG_ADMIN_CONTROLLER; ?></h1>

<div id="dashboard" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $dashboard_block) { ?>
    <div class="col <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : 'col1'); ?>" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
        <div class="actions-toolbar">
            <span class="drag"></span>
        </div>
        <h3><?php echo $dashboard_block['title']; ?></h3>
        <div class="col-body"><?php echo $dashboard_block['html']; ?></div>
    </div>
<?php } ?>
</div>
