<?php

    if ($do === 'add') { $this->setPageTitle(LANG_CP_CTYPES_ADD); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CONTENT_TYPE . ': ' . $ctype['title']); }

    if ($do === 'add'){
        $this->addBreadcrumb(LANG_CP_CTYPES_ADD);
    }

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => 'javascript:icms.forms.submit()'
    ]);

    $this->addToolButton([
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_BASIC,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->renderForm($form, $ctype, [
        'action' => '',
        'method' => 'post'
    ], $errors);
?>

<script>
    $(function(){
        $('#f_name .input').on('input', function(){
            $('#f_url_pattern .prefix').text('/'+$(this).val()+'/');
        });
    });
</script>