<?php

$this->setPageTitle($system['title']);

$this->addBreadcrumb(LANG_BILLING_CP_SYSTEMS, $this->href_to('systems'));
$this->addBreadcrumb($system['title']);

$this->addToolButton([
    'class' => 'save process-save',
    'title' => LANG_SAVE,
    'href'  => '#',
    'icon'  => 'save'
]);

$this->addToolButton([
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('systems'),
    'icon'  => 'undo'
]);

$this->renderForm($form, $system, [
    'action' => '',
    'method' => 'post'
], $errors);
