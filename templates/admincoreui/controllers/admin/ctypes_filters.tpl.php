<?php

    $this->setPageTitle(LANG_CP_CTYPE_FILTERS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS);

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('datasets', $ctype['id']));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FILTERS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    if($table_exists){
        $this->addToolButton(array(
            'class' => 'add',
            'title' => LANG_CP_FILTER_ADD,
            'href'  => $this->href_to('ctypes', array('filters_add', $ctype['id']))
        ));
        $this->addToolButton(array(
            'class' => 'view_list',
            'title' => LANG_CP_CTYPE_TO_LIST,
            'href'  => $this->href_to('ctypes')
        ));
    }
?>

<?php if(!$table_exists){ ?>
    <p class="alert alert-info mt-4" role="alert">
        <?php printf(LANG_CP_FILTER_NO_TABLE, $this->href_to('ctypes', array('filters_enable', $ctype['id'])) . '?back=' . href_to_current()); ?>
    </p>
<?php } else { ?>

<?php $this->renderGrid($this->href_to('ctypes', array('filters', $ctype['id'])), $grid); ?>

<?php } ?>