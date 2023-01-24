<?php

$this->setPageTitle($do === 'add' ? LANG_ADD_CATEGORY : LANG_EDIT_CATEGORY);

$this->addBreadcrumb($do === 'add' ? LANG_ADD_CATEGORY : LANG_EDIT_CATEGORY);

$this->addToolButton([
    'class' => 'save',
    'title' => LANG_SAVE,
    'href'  => 'javascript:icms.forms.submit()'
]);

$this->addToolButton([
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $back_url ? $back_url : href_to($ctype['name'])
]);

$category['ctype_name'] = $ctype['name'];

$this->renderForm($form, $category, [
    'action'  => '',
    'cancel'  => ['show' => true, 'href' => $back_url ? $back_url : href_to($ctype['name'])],
    'method'  => 'post',
    'toolbar' => true
], $errors);
