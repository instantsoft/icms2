<?php

    $this->setPageTitle(LANG_CP_SECTION_CTYPES);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_CTYPES_ADD,
        'href'  => $this->href_to('ctypes', array('add'))
    ));

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE_ORDER,
        'href'  => null,
        'onclick' => "icms.datagrid.submit('{$this->href_to('ctypes', array('reorder'))}')"
    ));

    $this->addToolButton(array(
        'class' => 'help',
        'title' => LANG_HELP,
        'target' => '_blank',
        'href'  => LANG_HELP_URL_CTYPES
    ));

?>

<h1><?php echo LANG_CP_SECTION_CTYPES; ?></h1>

<?php $this->renderGrid($this->href_to('ctypes', array('ajax')), $grid); ?>