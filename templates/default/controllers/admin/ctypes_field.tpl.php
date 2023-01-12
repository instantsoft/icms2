<?php if ($do=='add') { ?><h1><?php echo LANG_CP_FIELD_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_FIELD; ?>: <span><?php echo $field['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_FIELD_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_FIELD . ': ' . $field['title']); }

    if ($do=='add'){

        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_FIELD_ADD);
    }

    if ($do=='edit'){

        $this->addBreadcrumb(LANG_CP_CTYPE_FIELDS, $this->href_to('ctypes', array('fields', $ctype['id'])));
        $this->addBreadcrumb($field['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', array('fields', $ctype['id']))
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_FIELD
	));

    $this->renderControllerChild('admin', 'form_field', array(
        'fields_options_link' => $this->href_to('ctypes', array('fields_options')),
        'ctype_name'          => $ctype['name'],
        'do'                  => $do,
        'errors'              => $errors,
        'form'                => $form,
        'field'               => $field
    ));
