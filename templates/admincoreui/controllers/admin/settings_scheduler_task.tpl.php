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
    'class' => 'save process-save',
    'title' => LANG_SAVE,
    'href'  => '#',
    'icon'  => 'save'
]);

$this->addToolButton([
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('settings', ['scheduler']),
    'icon'  => 'undo'
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
    'method' => 'post',
    'cancel' => ['title' => LANG_CANCEL, 'href' => $this->href_to('settings', ['scheduler']), 'show' => true]
], $errors);
