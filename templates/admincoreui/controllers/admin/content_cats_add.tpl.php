<?php

$this->setPageTitle(LANG_CP_CONTENT_CATS_ADD);
$this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));
$this->addBreadcrumb($ctype['title'], $this->href_to('content', $ctype['id']));
$this->addBreadcrumb(LANG_CP_CONTENT_CATS_ADD);

$this->addToolButton([
    'class' => 'save process-save',
    'title' => LANG_SAVE,
    'href'  => '#',
    'icon'  => 'save'
]);

$this->addToolButton([
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $back_url ? $back_url : $this->href_to('content'),
    'icon'  => 'undo'
]);

$this->addMenuItem('breadcrumb-menu', [
    'title' => LANG_HELP,
    'url'   => LANG_HELP_URL_CONTENT_CATS,
    'options' => [
        'target' => '_blank',
        'icon' => 'question-circle'
    ]
]);

$this->renderForm($form, $category, [
    'action' => '',
    'method' => 'post'
], $errors);
