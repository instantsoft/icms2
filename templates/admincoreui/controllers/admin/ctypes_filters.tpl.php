<?php

    $this->setPageTitle(LANG_CP_CTYPE_FILTERS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_FILTERS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    if($table_exists){

        $this->addToolButton([
            'class' => 'add',
            'title' => LANG_CP_FILTER_ADD,
            'href'  => $this->href_to('ctypes', ['filters_add', $ctype['id']])
        ]);

        $this->addToolButton([
            'class' => 'view_list',
            'title' => LANG_CP_CTYPE_TO_LIST,
            'href'  => $this->href_to('ctypes')
        ]);
    }
?>

<?php if(!$table_exists){ ?>
    <p class="alert alert-info mt-4" role="alert">
        <?php printf(LANG_CP_FILTER_NO_TABLE, $this->href_to('ctypes', ['filters_enable', $ctype['id']]) . '?back=' . href_to_current()); ?>
    </p>
<?php } else { ?>

<?php $this->renderGrid($this->href_to('ctypes', ['filters', $ctype['id']]), $grid); ?>

<?php } ?>