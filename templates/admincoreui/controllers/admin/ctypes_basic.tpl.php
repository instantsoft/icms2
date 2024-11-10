<?php

    if ($do === 'add') { $this->setPageTitle(LANG_CP_CTYPES_ADD); }
    if ($do === 'edit') { $this->setPageTitle(LANG_CONTENT_TYPE . ': ' . $ctype['title']); }

    if ($do === 'add'){
        $this->addBreadcrumb(LANG_CP_CTYPES_ADD);
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'icon'  => 'list',
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
<?php ob_start(); ?>
<script>
    $(function(){
        $('#f_name .input').on('input', function(){
            $('#f_url_pattern .prefix').text('/'+$(this).val()+'/');
        });
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>