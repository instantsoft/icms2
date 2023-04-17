<?php

if ($do === 'add') {
    $page_title = LANG_CP_SCHEDULER_TASK_ADD;
}
if ($do === 'edit') {
    $page_title = LANG_CP_SCHEDULER_TASK_EDIT;
}

$this->setPageTitle($page_title);
$this->addBreadcrumb($page_title);

$this->addToolButton([
    'class' => 'save',
    'title' => LANG_SAVE,
    'href'  => 'javascript:icms.forms.submit()'
]);

$this->addToolButton([
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('settings', ['scheduler'])
]);

$this->addMenuItem('breadcrumb-menu', [
    'title'   => LANG_HELP,
    'url'     => LANG_HELP_URL_SETTINGS_SCHEDULER_TASK,
    'options' => [
        'target' => '_blank',
        'icon'   => 'question-circle'
    ]
]);

$this->renderForm($form, $task, [
    'action' => '',
    'method' => 'post'
], $errors);
