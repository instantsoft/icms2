<?php

    $this->addTplJSName([
        'admin-widgets-page'
    ]);

    if ($do=='add') { $this->setPageTitle(LANG_CP_WIDGETS_ADD_PAGE); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_WIDGETS_PAGE.': '.$page['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_WIDGETS_ADD_PAGE);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($page['title']);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_WIDGETS_PAGES,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('widgets'),
        'icon'  => 'undo'
    ]);
?>
<div id="admin-widgets-page" data-fast_add_submit="<?php echo LANG_CP_WIDGETS_FA_ADD; ?>" data-autocomplete_url="<?php echo href_to('admin', 'widgets', 'page_autocomplete'); ?>">
<?php
    $this->renderForm($form, $page, [
        'action' => '',
        'method' => 'post'
    ], $errors);

?>
</div>