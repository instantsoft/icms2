
<?php

    $page_title = $do=='add' ? LANG_GROUPS_ADD : LANG_GROUPS_EDIT;

    $this->setPageTitle($page_title);
    $this->addBreadcrumb(LANG_GROUPS, $this->href_to(''));
    if ($do=='edit'){
        $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    }
    $this->addBreadcrumb($page_title);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => "javascript:goBack()"
    ));

?>

<h1><?php echo $page_title ?></h1>

<?php if ($do=='edit') { $this->renderChild('group_edit_header', array('group'=>$group)); } ?>

<?php
    $this->renderForm($form, $group, array(
        'action' => '',
        'toolbar' => false,
        'method' => 'post'
    ), $errors);
