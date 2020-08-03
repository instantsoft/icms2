<?php

    $this->addTplJSName([
        'jquery-cookie',
        'datatree',
        'admin-widgets'
        ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_WIDGETS);
    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_WIDGETS_ADD_PAGE,
        'href'  => $this->href_to('widgets', 'page_add')
    ));
    $this->addToolButton(array(
        'class' => 'edit',
        'title' => LANG_CP_WIDGETS_EDIT_PAGE,
        'href'  => $this->href_to('widgets', 'page_edit')
    ));
    $this->addToolButton(array(
        'class' => 'delete',
        'title' => LANG_CP_WIDGETS_DELETE_PAGE,
        'href'  => $this->href_to('widgets', 'page_delete')
    ));
    $this->addToolButton(array(
        'class'   => 'move',
        'title'   => LANG_CP_WIDGETS_UNBIND_ALL_WIDGETS,
        'onclick' => "return confirm('" .LANG_CP_WIDGETS_UNBIND_ALL_WIDGETS_CONFIRM. "')",
        'href'    => $this->href_to('widgets', array('unbind_all_widgets', $template_name))
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_WIDGETS
	));

    $this->applyToolbarHook('admin_widgets_toolbar');

?>

<h1><?php echo LANG_CP_SECTION_WIDGETS; ?></h1>

<table class="layout">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
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

        </td>
        <td class="main" valign="top" style="padding-right:10px">

            <div id="cp-widgets-select-template" data-current_url="<?php echo $this->href_to('widgets'); ?>">
                <?php echo LANG_CP_WIDGETS_TEMPLATE; ?> <?php echo html_select('template', $templates, $template_name); ?>
            </div>
            <div class="cp_toolbar">
                <?php $this->toolbar(); ?>
            </div>

            <div id="cp-widgets-layout"
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
                <?php if(!$is_dynamic_scheme){ ?>
                    <?php echo $scheme_html; ?>
                <?php } else { ?>
                    <p><?php echo LANG_CP_WIDGETS_DSCH_ERROR; ?></p>
                <?php } ?>
                <div id="cp-widgets-unused">
                    <h3><?php echo LANG_CP_WIDGETS_UNUSED; ?></h3>
                    <ul class="position" rel="_unused" id="pos-_unused"></ul>
                    <div class="hint"><?php echo LANG_CP_WIDGETS_UNUSED_HINT; ?></div>
                </div>
                <div id="cp-widgets-bind">
                    <h3><?php echo LANG_CP_WIDGETS_BINDED; ?></h3>
                    <ul class="position" rel="_copy" id="pos-_copy"></ul>
                    <div class="hint"><?php echo LANG_CP_WIDGETS_BINDED_HINT; ?></div>
                </div>
            </div>

        </td>
        <td class="sidebar" valign="top" width="150">

            <div id="cp-widgets-list">

                <?php if ($widgets_list){ ?>

                    <div id="accordion">

                        <?php foreach($widgets_list as $controller_name=>$widgets){ ?>

                            <div class="section">

                                <?php $controller_title = $controller_name ? constant("LANG_".mb_strtoupper($controller_name)."_CONTROLLER") : LANG_CP_WIDGETS_MISC; ?>

                                <a class="section-open" href="#" rel="<?php echo $controller_name; ?>"><span>&rarr;</span> <?php echo $controller_title; ?></a>
                                <ul>
                                    <?php foreach($widgets as $widget){ ?>
                                        <li rel="new" data-id="<?php echo $widget['id']; ?>">
                                            <?php echo $widget['title']; ?>
                                            <?php if($widget['is_external']){ ?>
                                                <span class="actions">
                                                    <a class="delete" href="#" title="<?php echo LANG_DELETE; ?>"></a>
                                                </span>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>

                            </div>

                        <?php } ?>

                    </div>

                <?php } ?>

                <div id="actions-template" style="display:none">
                    <span class="actions">
                        <a class="hide" href="#" onclick="return widgetToggle(this)" title="<?php echo LANG_HIDE; ?>"></a>
                        <a class="copy" href="#" onclick="return widgetCopy(this)" title="<?php echo LANG_COPY; ?>"></a>
                        <a class="edit" href="#" onclick="return widgetEdit(this)" title="<?php echo LANG_EDIT; ?>"></a>
                        <a class="delete" href="#" onclick="return widgetDelete(this)" title="<?php echo LANG_DELETE; ?>"></a>
                    </span>
                </div>

            </div>

        </td>
    </tr>
</table>

<script>
    <?php echo $this->getLangJS('LANG_CP_WIDGET_COPY_CONFIRM', 'LANG_CP_WIDGET_DELETE_CONFIRM', 'LANG_CP_WIDGET_REMOVE_CONFIRM', 'LANG_CP_PACKAGE_CONTENTS', 'LANG_HIDE', 'LANG_SHOW'); ?>
</script>