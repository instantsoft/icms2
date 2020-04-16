<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_CTYPES_ADD); }
    if ($do=='edit') { $this->setPageTitle(LANG_CONTENT_TYPE . ': ' . $ctype['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_CTYPES_ADD);
        $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('add'));
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title']);
        $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('edit', $id));
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_BASIC,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

    $this->renderForm($form, $ctype, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script>
    $(function(){
        $('#f_name .input').on('input', function(){
            $('#f_url_pattern .prefix').html('/'+$(this).val()+'/');
        });
    });
</script>