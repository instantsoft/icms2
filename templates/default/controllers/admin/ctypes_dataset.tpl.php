<?php if ($do=='add') { ?><h1><?php echo LANG_CP_DATASET_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_DATASET; ?>: <span><?php echo $dataset['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_DATASET_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_DATASET . ': ' . $dataset['title']); }

    if(!$ctype['id']){

        $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));
        $this->addBreadcrumb($ctype['title'], $this->href_to('controllers', 'edit/'.$ctype['name']));
    }

    if($ctype['id']){
        $cancel_url = $this->href_to('ctypes', array('datasets', $ctype['id']));
    } else {
        $cancel_url = $this->href_to('controllers', 'edit/'.$ctype['name'].'/datasets');
    }

    if ($do=='add'){

        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $cancel_url);
        $this->addBreadcrumb(LANG_CP_DATASET_ADD);

    }

    if ($do=='edit'){

        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $cancel_url);
        $this->addBreadcrumb($dataset['title']);
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
		'href'   => LANG_HELP_URL_CTYPES_DATASET
	));

    $this->renderForm($form, $dataset, array(
        'action'  => '',
        'form_id' => 'dataset_form',
        'method'  => 'post'
    ), $errors);
