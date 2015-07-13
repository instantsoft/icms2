<?php

    $page_title = $ctype['title'] . ': <span>' . LANG_CP_CONTENT_CATS_ADD . '</span>';

    $this->setPageTitle(LANG_CP_CONTENT_CATS_ADD);
    $this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));
    $this->addBreadcrumb(LANG_CP_CONTENT_CATS_ADD);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $back_url ? $back_url : $this->href_to('content')
    ));

    $this->addToolButton(array(
        'class' => 'help',
        'title' => LANG_HELP,
        'target' => '_blank',
        'href'  => LANG_HELP_URL_CONTENT_CATS
    ));

?>

<h1><?php echo $page_title ?></h1>

<?php

    $category['ctype_name'] = $ctype['name'];

    $this->renderForm($form, $category, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

?>
