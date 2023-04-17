<?php

    $this->addTplJSName([
        'datatree',
        'admin-widgets',
        'vendors/filesaver.min'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_WIDGETS);
    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('widgets').'?last');

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_WIDGETS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

	$this->addToolButton(array(
        'class' => 'menu d-xl-none',
		'data'  => [
            'toggle' =>'quickview',
            'toggle-element' => '#left-quickview'
        ],
		'title' => LANG_MENU
	));

    $this->addToolButton(array(
        'class' => 'view_list',
        'childs_count' => 2,
        'title' => LANG_CP_WIDGETS_PAGES,
        'href'  => ''
    ));
    $this->addToolButton(array(
        'class' => 'add',
        'level' => 2,
        'title' => LANG_CP_WIDGETS_ADD_PAGE,
        'href'  => $this->href_to('widgets', 'page_add')
    ));
    $this->addToolButton(array(
        'class' => 'edit',
        'level' => 2,
        'title' => LANG_CP_WIDGETS_EDIT_PAGE,
        'href'  => $this->href_to('widgets', 'page_edit')
    ));
    $this->addToolButton(array(
        'class' => 'delete',
        'level' => 2,
        'title' => LANG_CP_WIDGETS_DELETE_PAGE,
        'href'  => $this->href_to('widgets', 'page_delete')
    ));

    $this->addToolButton(array(
        'class'   => 'cancel',
        'title'   => LANG_CP_WIDGETS_UNBIND_ALL_WIDGETS,
        'onclick' => "return confirm('" .LANG_CP_WIDGETS_UNBIND_ALL_WIDGETS_CONFIRM. "')",
        'href'    => $this->href_to('widgets', array('unbind_all_widgets', $template_name))
    ));

    $this->addToolButton(array(
        'class' => 'gridicon',
        'childs_count' => count($templates),
        'title' => LANG_CP_WIDGETS_TEMPLATE.': '.$templates[$template_name],
        'href'  => ''
    ));

    foreach ($templates as $tkey => $template) {
        $this->addToolButton(array(
            'level' => 2,
            'title' => $template,
            'href'  => $this->href_to('widgets').'?template_name='.$tkey
        ));
    }

    if($is_dynamic_scheme){
        $this->addToolButton(array(
            'class' => 'add add_row ajax-modal',
            'title' => LANG_CP_WIDGETS_ADD_ROW,
            'href'  => $this->href_to('widgets', ['row_add', $template_name])
        ));
        $this->addToolButton(array(
            'class' => 'install add_row ajax-modal',
            'title' => LANG_CP_WIDGETS_IMPORT_SCHEME,
            'href'  => $this->href_to('widgets', ['import_scheme', $template_name])
        ));
        $this->addToolButton(array(
            'class' => 'export ajax-modal',
            'title' => LANG_CP_WIDGETS_EXPORT_SCHEME,
            'href'  => $this->href_to('widgets', ['export_scheme', $template_name])
        ));
    }

    $this->applyToolbarHook('admin_widgets_toolbar');

?>

<div class="row align-items-stretch mb-4">
    <div class="col-sm-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div class="card-body bg-white h-100 pt-3 no-overflow">
            <div class="quickview-wrapper__sticky-wraper" id="intro-step1">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#datatree"><?php echo LANG_CP_WIDGETS_PAGES; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#all-widgets"><?php echo LANG_CP_WIDGETS_ALL; ?></a>
                    </li>
                </ul>
                <div class="tab-content border-right-0 border-bottom-0 border-left-0">
                    <div class="tab-pane p-0 pt-2 show active" id="datatree" role="tabpanel">
                        <ul id="treeData" class="skeleton-tree">
                            <li id="core" class="folder">
                                <?php echo LANG_WP_SYSTEM; ?>
                                <ul>
                                    <li id="core.0"><?php echo LANG_WP_ALL_PAGES; ?></li>
                                    <li id="core.1"><?php echo LANG_WP_HOME_PAGE; ?></li>
                                </ul>
                            </li>
                            <?php foreach($controllers as $controller_name => $controller_title){ ?>
                                <li id="<?php echo $controller_name ? $controller_name : 'custom'; ?>" class="lazy folder"><?php echo $controller_title; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="tab-pane p-0 pt-2" id="all-widgets" role="tabpanel">
                        <div id="cp-widgets-list" class="mt-3">
                            <?php if ($widgets_list){ ?>
                                <div id="accordion">
                                    <?php foreach($widgets_list as $controller_name => $widgets){ ?>
                                        <div class="section">
                                            <?php $controller_title = $controller_name ? constant("LANG_".mb_strtoupper($controller_name)."_CONTROLLER") : LANG_CP_WIDGETS_MISC; ?>
                                            <a class="btn btn-primary btn-block mb-1 text-left rounded-0" href="#" rel="<?php echo $controller_name; ?>" data-toggle="collapse" data-target="#w-<?php echo $controller_name; ?>">
                                                <?php echo $controller_title; ?>
                                            </a>
                                            <ul class="mt-3 px-2 list-unstyled collapse <?php echo !$controller_name ? 'show' : ''; ?>" id="w-<?php echo $controller_name; ?>" data-parent="#accordion">
                                                <?php foreach($widgets as $widget){ ?>
                                                <li rel="new" data-id="<?php echo $widget['id']; ?>">
                                                        <span class="title">
                                                            <?php echo $widget['title']; ?>
                                                            <?php if($widget['is_external']){ ?>
                                                                <sup><?php echo $widget['version']; ?></sup>
                                                            <?php } ?>
                                                        </span>
                                                        <?php if($widget['is_external']){ ?>
                                                            <span class="actions float-md-right d-flex">
                                                                <a class="delete" href="#" title="<?php echo LANG_DELETE; ?>">
                                                                    <i class="icon-close icons font-xl d-block"></i>
                                                                </a>
                                                            </span>
                                                        <?php } ?>
                                                        <?php if($widget['image_hint_path']){ ?>
                                                            <img src="<?php echo $widget['image_hint_path']; ?>">
                                                        <?php } ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm">
        <?php if ($this->isToolbar()){ ?>
            <?php $this->toolbar('menu-toolbar'); ?>
        <?php } ?>
        <div id="cp-widgets-layout"
             data-scheme-row-reorder-url="<?php echo href_to('admin', 'reorder', ['layout_rows']); ?>"
             data-scheme-col-reorder-url="<?php echo href_to('admin', 'reorder', ['layout_cols']); ?>"
             data-scheme-row-add-url="<?php echo $this->href_to('widgets', ['row_add', $template_name]); ?>"
             data-template="<?php echo $template_name; ?>"
             data-toggle-url="<?php echo $this->href_to('widgets', 'toggle'); ?>"
             data-tree-url="<?php echo $this->href_to('widgets', 'tree_ajax'); ?>"
             data-load-url="<?php echo $this->href_to('widgets', 'load'); ?>"
             data-add-url="<?php echo $this->href_to('widgets', 'add'); ?>"
             data-edit-url="<?php echo $this->href_to('widgets', 'edit'); ?>"
             data-delete-url="<?php echo $this->href_to('widgets', 'delete'); ?>"
             data-remove-url="<?php echo $this->href_to('widgets', 'remove'); ?>"
             data-copy-url="<?php echo $this->href_to('widgets', 'copy'); ?>"
             data-files-url="<?php echo $this->href_to('package_files_list', 'widgets'); ?>"
             data-edit-page-url="<?php echo $this->href_to('widgets', 'page_edit'); ?>"
             data-delete-page-url="<?php echo $this->href_to('widgets', 'page_delete'); ?>"
             data-reorder-url="<?php echo $this->href_to('widgets', 'reorder'); ?>"
             >
            <?php echo $scheme_html; ?>
            <?php if($is_dynamic_scheme){ ?>
                <div class="row my-3 justify-content-end">
                    <div class="col-sm-auto ml-auto text-muted border-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="form-check-input input-checkbox custom-control-input" name="show_all_wd" value="1" id="show_all_wd" checked="checked">
                            <label class="custom-control-label" for="show_all_wd"><?php echo LANG_CP_WIDGETS_SHOW_ALL; ?></label>
                        </div>
                    </div>
                    <div class="col-sm-auto text-muted" id="rows_titles_pos">
                        <span class="d-inline-block mr-3"><?php echo LANG_CP_WIDGETS_ROWS_TITLE; ?></span>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input <?php if($rows_titles_pos == 'left'){ ?>checked<?php } ?> class="custom-control-input" type="radio" id="r-left" name="rows_titles_pos" value="left">
                            <label class="custom-control-label" for="r-left"><?php echo LANG_CP_FIELD_LABEL_LEFT; ?></label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input <?php if($rows_titles_pos == 'top'){ ?>checked<?php } ?> class="custom-control-input" type="radio" id="r-top" name="rows_titles_pos" value="top">
                            <label class="custom-control-label" for="r-top"><?php echo LANG_CP_FIELD_LABEL_TOP; ?></label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline mr-0">
                            <input <?php if($rows_titles_pos == 'hide'){ ?>checked<?php } ?> class="custom-control-input" type="radio" id="r-hide" name="rows_titles_pos" value="hide">
                            <label class="custom-control-label" for="r-hide"><?php echo LANG_HIDE; ?></label>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div id="cp-widgets-unused" class="alert alert-secondary mt-3 mb-0">
                <h5><?php echo LANG_CP_WIDGETS_UNUSED; ?></h5>
                <ul class="position" rel="_unused" id="pos-_unused"></ul>
                <div class="hint text-muted small"><?php echo LANG_CP_WIDGETS_UNUSED_HINT; ?></div>
            </div>
            <div id="cp-widgets-bind" class="alert alert-info mt-2 mb-0">
                <h5><?php echo LANG_CP_WIDGETS_BINDED; ?></h5>
                <ul class="position" rel="_copy" id="pos-_copy"></ul>
                <div class="hint text-muted small"><?php echo LANG_CP_WIDGETS_BINDED_HINT; ?></div>
            </div>
        </div>
    </div>
</div>
<div id="actions-template" style="display:none">
    <span class="actions float-lg-right d-flex">
        <a class="hide mr-3" href="#" data-func="widgetToggle" title="<?php echo LANG_HIDE; ?>"><i class="icon-check icons font-xl d-block"></i></a>
        <a class="copy mr-3" href="#" data-func="widgetCopy" title="<?php echo LANG_COPY; ?>"><i class="icon-docs icons font-xl d-block"></i></a>
        <a class="edit mr-3" href="#" data-func="widgetEdit" title="<?php echo LANG_EDIT; ?>"><i class="icon-pencil icons font-xl d-block"></i></a>
        <a class="delete" href="#" data-func="widgetDelete" title="<?php echo LANG_DELETE; ?>"><i class="icon-close icons font-xl d-block"></i></a>
    </span>
</div>
<script>
    <?php echo $this->getLangJS('LANG_CP_WIDGET_COPY_CONFIRM', 'LANG_CP_WIDGET_DELETE_CONFIRM', 'LANG_CP_WIDGET_REMOVE_CONFIRM', 'LANG_CP_PACKAGE_CONTENTS', 'LANG_HIDE', 'LANG_SHOW'); ?>
    $(function(){
        icms.admin.introJsInit({page: 'widgets', steps: <?php echo json_encode($intro_lang); ?>});
        <?php if($scroll_to) { ?>
            $(function(){
                var el = $("#<?php html($scroll_to); ?>").addClass('shadow');
                $('html, body').animate({
                    scrollTop: el.offset().top + 150
                }, 500);
                setTimeout(function (){
                    el.removeClass('shadow');
                }, 5000);
            });
        <?php } ?>
    });
</script>
