<?php

    $this->setPageTitle(LANG_EDIT_FOLDER);

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    $this->addBreadcrumb(LANG_EDIT_CATEGORY);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'icon'  => 'undo',
        'title' => LANG_CANCEL,
        'href'  => href_to($ctype['name'])
    ]);

?>

<h1><?php echo LANG_EDIT_FOLDER; ?></h1>

<?php

    $this->renderForm($form, $folder, [
        'action' => '',
        'method' => 'post',
        'toolbar' => false
    ], $errors);
