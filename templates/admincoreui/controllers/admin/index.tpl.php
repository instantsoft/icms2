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
            'icon'  => 'sliders-h'
        ]
    ]);
?>

<div id="dashboard" class="animated fadeIn row align-content-around" data-save_order_url="<?php echo $this->href_to('index_save_order'); ?>">
<?php foreach ($dashboard_blocks as $dashboard_block) { ?>
    <div class="is-sortable <?php echo (isset($dashboard_block['class']) ? $dashboard_block['class'] : 'col-md-6 '); ?> mb-4" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
        <div class="card mb-0 h-100 <?php echo (isset($dashboard_block['child_class']) ? $dashboard_block['child_class'] : ''); ?>" id="db_<?php echo $dashboard_block['name']; ?>" data-name="<?php echo $dashboard_block['name']; ?>">
            <?php if(empty($dashboard_block['hide_title'])){ ?>
                <div class="card-header">
                    <span class="db-sortable-handle"><?php echo $dashboard_block['title']; ?></span>
                    <span class="db_spinner sk-spinner sk-spinner-pulse bg-blue ml-3" style="display: none"></span>
                    <?php if(!empty($dashboard_block['counter'])){ ?>
                        <span class="badge py-1 badge-pill badge-success float-right"> <?php echo $dashboard_block['counter']; ?></span>
                    <?php } ?>
                    <?php if(!empty($dashboard_block['actions'])){ ?>
                        <div class="card-header-actions">
                            <?php foreach ($dashboard_block['actions'] as $act) { ?>
                                <a class="card-header-action" href="<?php echo $act['url']; ?>"<?php if(!empty($act['hint'])){ ?> title="<?php html($act['hint']); ?>"<?php } ?>>
                                    <?php if(!empty($act['icon'])){
                                        $icon_params = explode(':', $act['icon']);
                                        if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }
                                        html_svg_icon($icon_params[0], $icon_params[1]);
                                    } ?>
                                    <?php if(!empty($act['title'])){ ?>
                                        <?php echo $act['title']; ?>
                                    <?php } ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="card-body"><?php echo $dashboard_block['html']; ?></div>
        </div>
    </div>
<?php } ?>
</div>