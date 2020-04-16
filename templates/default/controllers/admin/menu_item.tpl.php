<?php if ($do=='add') { ?><h1><?php echo LANG_CP_MENU_ITEM_ADD; ?>: <span><?php echo $menu['title']; ?></span></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_MENU_ITEM; ?>: <span><?php echo $item['title']; ?></span></h1><?php } ?>

<?php    

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb(LANG_CP_MENU_ITEM_ADD);
		$this->setPageTitle(LANG_CP_MENU_ITEM_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb($item['title']);
		$this->setPageTitle(LANG_CP_MENU_ITEM.': '.$item['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('menu')
    ));

    $this->renderForm($form, $item, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
