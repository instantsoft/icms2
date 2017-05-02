<h1><?php echo $page_title ?></h1>

<?php if ($do=='edit') { $this->renderChild('group_edit_header', array('group' => $group)); } ?>

<?php
    if($group['slug'] == $group['id']){ $group['slug'] = null; }
    $this->renderForm($form, $group, array(
        'action'  => '',
        'cancel'  => array('show' => true, 'href' => 'javascript:goBack()'),
        'toolbar' => false,
        'method'  => 'post',
        'hook' => array(
            'event' => 'group_form_html',
            'param' => array(
                'do' => $do,
                'id' => $do=='edit' ? $group['id'] : null
            )
        )
    ), $errors);
