<?php
$this->addBreadcrumb(LANG_MODERATION_LOGS);
$this->setPageTitle(LANG_MODERATION_LOGS);

$this->addToolButton(array(
    'class'   => 'delete',
    'title'   => LANG_MODERATION_CLEAR_LOGS,
    'confirm' => LANG_MODERATION_CLEAR_LOGS_HINT,
    'href'    => $this->href_to('delete_log', $sub_url)
));
if(!empty($sub_url)){
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_SHOW_ALL,
        'href'  => $this->href_to('logs')
    ));
}

$this->renderGrid($url, $grid);
