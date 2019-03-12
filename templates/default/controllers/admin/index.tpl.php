<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addJS('templates/default/js/admin-chart.js');
    $this->addJS('templates/default/js/admin-dashboard.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    
    $this->addJS('templates/default/js/admin-dashboard__setup.js');
    $this->addCSS('templates/default/css/admin-dashboard__setup.css');
    
?>
<div class="widgets-menu" data-save_visible_url="<?php echo $this->href_to('index_save_visible'); ?>">
    <a href="#" class="toggle right" id="widget-toggle">
        <i class="material-icons">more_vert</i>
    </a>
    <ul class="dropdown-menu" data-menu data-menu-toggle="#widget-toggle">
        <?php foreach ($dashboard_blocks as $key => $dashboard_block) { ?>
            <li>
                <a class="dropdown-menu_link" data-id="<?php echo $dashboard_block['id']; ?>" href="#">
                    <i class="material-icons"><?php echo ($options['dashboard_visible'][$dashboard_block['id']] ? 'radio_button_checked' : 'radio_button_unchecked'); ?></i> <span><?php echo $dashboard_block['title']; ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<h1><?php echo LANG_ADMIN_CONTROLLER; ?></h1>

<div id="dashboard" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $key => $dashboard_block) { ?>
    <div class="col <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : 'col1'); ?> <?php echo ($options['dashboard_visible'][$dashboard_block['id']] ? '' : 'hidden'); ?>" id="db_<?php echo $dashboard_block['id']; ?>" data-id="<?php echo $dashboard_block['id']; ?>">
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
    });
</script>