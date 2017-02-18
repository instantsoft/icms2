<?php
$this->addBreadcrumb(LANG_MODERATION_LOGS);
$this->setPageTitle(LANG_MODERATION_LOGS);

$this->addToolButton(array(
    'class'   => 'delete',
    'title'   => LANG_MODERATION_CLEAR_LOGS,
    'confirm' => LANG_MODERATION_CLEAR_LOGS_HINT,
    'href'    => $this->href_to('delete_log', $sub_url).($url_query ? '?'.http_build_query($url_query) : '')
));
if(!empty($sub_url) || !empty($url_query)){
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_SHOW_ALL,
        'href'  => $this->href_to('logs')
    ));
}

$this->renderGrid($url, $grid);
