<?php if ($do=='add') { ?><h1><?php echo LANG_CP_WIDGETS_ADD_PAGE; ?></span></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_WIDGETS_PAGE; ?>: <span><?php echo $page['title']; ?></span></h1><?php } ?>

<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_WIDGETS_ADD_PAGE); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_WIDGETS_PAGE.': '.$page['title']); }
    
    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_WIDGETS_ADD_PAGE);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($page['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('widgets')
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_WIDGETS_PAGES
	));

    $this->renderForm($form, $page, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
