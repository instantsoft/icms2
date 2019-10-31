<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addTplJSName([
        'admin-chart',
        'admin-dashboard'
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_CP_PAGE_OPTIONS,
        'url'   => $this->href_to('index_page_settings'),
        'options' => [
            'title' => LANG_CONFIG,
            'class' => 'ajax-modal',
            'icon'  => 'icon-settings'
        ]
    ]);
?>

<div id="dashboard" class="animated fadeIn row align-content-around" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $dashboard_block) { ?>
    <div class="is-sortable <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : 'col-md-6 '); ?> mb-4" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
        <div class="card mb-0 h-100 <?php echo (isset($dashboard_block['child_class']) ? $dashboard_block['child_class'] : ''); ?>" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
            <?php if(empty($dashboard_block['hide_title'])){ ?>
                <div class="card-header">
                    <?php echo $dashboard_block['title']; ?> <span class="db_spinner sk-spinner sk-spinner-pulse bg-blue ml-3" style="display: none"></span>
                    <?php if(!empty($dashboard_block['counter'])){ ?>
                        <span class="badge py-1 badge-pill badge-success float-right"> <?php echo $dashboard_block['counter']; ?></span>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="card-body"><?php echo $dashboard_block['html']; ?></div>
        </div>
    </div>
<?php } ?>
</div>