<?php

    $this->addBreadcrumb(LANG_USERS_CFG_TABS, $this->href_to('tabs'));

    $this->addBreadcrumb($tab['title']);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('tabs')
    ));

?>

<?php
    $this->renderForm($form, $tab, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
