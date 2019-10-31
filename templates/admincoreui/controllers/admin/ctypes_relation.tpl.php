<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_RELATION_ADD, $ctype['title']); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_RELATION . ': ' . $relation['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS, $this->href_to('ctypes', array('relations', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_RELATION_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_RELATIONS, $this->href_to('ctypes', array('relations', $ctype['id'])));
        $this->addBreadcrumb($relation['title']);
    }

    $this->addMenuItems('admin_toolbar', $this->controller->getCtypeMenu('relations', $ctype['id']));

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', array('relations', $ctype['id']))
    ));

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_RELATIONS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
        ]
    ]);

?>

<?php $this->renderForm($form, $relation, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
<script>
    $(document).ready(function(){

        var isTitleTyped = $('input#title').val() != '';

        $('input#title').on('input', function(){
            isTitleTyped = true;
        });

        $('select#child_ctype_id').on('change', function(){
           if (!isTitleTyped){
               $('input#title').val($(this).find('option:selected').text().replace(/(.*): /gi, ''));
           }
        }).triggerHandler('change');

        $('select#layout').on('change', function(){
           $('form #tab-tab-opts fieldset').toggle( $(this).val() == 'tab' );
        }).triggerHandler('change');

    });
</script>