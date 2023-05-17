<?php

    $this->setPageTitle(LANG_CP_TEMPLATE_ICONS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SETTINGS_TEMPLATE_OPTIONS, $this->href_to('settings', ['theme', $template_name]));
    $this->addBreadcrumb(LANG_CP_TEMPLATE_ICONS);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

    $is_ajax = $this->controller->request->isAjax();
?>

<?php if(!$is_exists_list){ ?>
<p class="alert alert-warning m-0">
    <?php echo LANG_CP_TEMPLATE_NO_ICONS_SELECT; ?>
</p>
<?php return; } ?>
<?php
$active_tab = false;
$this->addTplJSNameFromContext(['vendors/list.min']);
?>
<ul class="nav nav-tabs">
    <?php foreach($icon_list as $tab_name => $list) { ?>
        <li class="nav-item">
            <a class="nav-link<?php if(!$active_tab){ $active_tab = $tab_name; ?> active<?php } ?>" href="#tab-<?php echo $tab_name; ?>" data-toggle="tab">
                <?php echo $tab_name; ?>
            </a>
        </li>
    <?php } ?>
    <li class="nav-item ml-auto align-items-center d-flex">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="copy-dev">
            <label class="custom-control-label" for="copy-dev"><?php echo LANG_CP_TEMPLATE_ICONS_PHP; ?></label>
        </div>
    </li>
</ul>
<div class="tab-content<?php if(!$is_ajax){ ?> mb-4<?php } ?>">
<?php foreach($icon_list as $tab_name => $list) { ?>
    <div id="tab-<?php echo $tab_name; ?>" class="tab-pane<?php if($active_tab == $tab_name){ ?> active<?php } ?>">
        <div class="row mt-3">
            <div class="col-sm">
                <input type="search" class="form-control search" placeholder="<?php echo LANG_FIND; ?>">
            </div>
        </div>
        <div class="row list mt-4 mb-n4">
            <?php foreach($list as $icon) { ?>
            <a class="col-4 col-sm-3 col-lg-2 text-center text-primary text-decoration-none mb-4 icon-select" href="#" data-name="<?php echo $icon['name']; ?>">
                <span class="icon-data h2 d-block">
                    <?php echo $icon['html']; ?>
                </span>
                <span class="icon-name small d-block">
                    <?php echo !empty($icon['title']) ? $icon['title'] : $icon['name']; ?>
                </span>
            </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>
</div>
<?php ob_start(); ?>
<script>
    var copy_as_php = false;
    $(function (){
        $('#copy-dev').on('click', function (){
            copy_as_php = $(this).is(':checked');
        });
        setTimeout(function (){
        <?php foreach($icon_list as $tab_name => $tab) { ?>
            new List('tab-<?php echo $tab_name; ?>', {valueNames: ['icon-name']});
        <?php } ?>
        }, 300);
        <?php if(!$is_ajax){ ?>
            $('.icon-select').on('click', function (){
                if(!copy_as_php){
                    icms.admin.copyToBuffer('{'+$(this).data('name').replace(':', '%')+'}');
                } else {
                    let icon = $(this).data('name').split(':');
                    icms.admin.copyToBuffer("<?php echo '<?php'; ?> html_svg_icon('"+icon[0]+"', '"+icon[1]+"'); <?php echo '?>'; ?>");
                }
                return false;
            });
        <?php } ?>
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>