<?php if ($do=='add') { ?><h1><?php echo LANG_CP_FILTER_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_GRID_COLYMNS_FILTER; ?>: <span><?php echo $filter['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FILTER_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_GRID_COLYMNS_FILTER . ': ' . $filter['title']); }

    $cancel_url = $this->href_to('ctypes', array('filters', $ctype['id']));

    if ($do=='add'){

        $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS, $cancel_url);
        $this->addBreadcrumb(LANG_CP_FILTER_ADD);

    }

    if ($do=='edit'){

        $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS, $cancel_url);
        $this->addBreadcrumb($filter['title']);

    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ));
    $this->addToolButton(array(
		'class'  => 'help',
		'title'  => LANG_HELP,
		'target' => '_blank',
		'href'   => LANG_HELP_URL_CTYPES_FILTERS
	));

    $this->renderForm($form, $filter, array(
        'action'  => '',
        'form_id' => 'filter_form',
        'method'  => 'post'
    ), $errors);
