<?php if ($do=='add') { ?><h1><?php echo LANG_CP_MENU_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_MENU; ?>: <span><?php echo $item['title']; ?></span></h1><?php } ?>

<?php

    $this->setPageTitle($do == 'add' ? LANG_CP_MENU_ADD : LANG_CP_MENU.': '.$item['title']);

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb(LANG_CP_MENU_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb(LANG_CP_MENU, $this->href_to('menu'));
        $this->addBreadcrumb($item['title']);
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
