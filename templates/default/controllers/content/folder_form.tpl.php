<?php

    $this->setPageTitle(LANG_EDIT_FOLDER);

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    $this->addBreadcrumb(LANG_EDIT_CATEGORY);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => href_to($ctype['name'])
    ));

?>

<h1><?php echo LANG_EDIT_FOLDER; ?></h1>

<?php

    $this->renderForm($form, $folder, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false
    ), $errors);
