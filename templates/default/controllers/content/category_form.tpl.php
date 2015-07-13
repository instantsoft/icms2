<?php

    $page_title =   $do=='add' ?
                    $ctype['title'] . ': <span>' . LANG_ADD_CATEGORY . '</span>':
                    $ctype['title'] . ': <span>' . LANG_EDIT_CATEGORY . '</span>';

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    $this->addBreadcrumb($do=='add' ? LANG_ADD_CATEGORY : LANG_EDIT_CATEGORY);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $back_url ? $back_url : href_to($ctype['name'])
    ));

?>

<h1><?php echo $page_title ?></h1>

<?php

    $category['ctype_name'] = $ctype['name'];

    $this->renderForm($form, $category, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false
    ), $errors);

?>
