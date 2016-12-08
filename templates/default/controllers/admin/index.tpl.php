<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addJS('templates/default/js/admin-chart.js');
    $this->addJS('templates/default/js/admin-dashboard.js');
    $this->addJS('templates/default/js/col-auto-height.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
?>
<h1><?php echo LANG_ADMIN_CONTROLLER; ?></h1>

<div id="dashboard" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $key => $dashboard_block) { ?>
    <div class="col <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : 'col1'); ?>" id="db_<?php echo $dashboard_block['id']; ?>" data-id="<?php echo $dashboard_block['id']; ?>">
        <div class="actions-toolbar">
            <span class="drag"></span>
        </div>
        <h3><?php echo $dashboard_block['title']; ?></h3>
        <div class="col-body"><?php echo $dashboard_block['html']; ?></div>
    </div>
<?php } ?>
</div>
<script>
    $(function() {
        $(document).tooltip({
            items: '.tooltip',
            show: { duration: 0 },
            hide: { duration: 0 },
            position: {
                my: "center",
                at: "top-20"
            }
        });
        colAutoHeight.init('#dashboard', '.col');
    });
</script>