<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    $users_model = cmsCore::getModel('users');

    $items = $field->getProperty('show_all') ? array(0 => LANG_ALL) : array();
    $is_show_guests = (bool)$field->getProperty('show_guests');

    $groups = $users_model->getGroups( $is_show_guests );

    foreach ($groups as $group){
        $items[$group['id']] = $group['title'];
    }

    if (!$value) { $value = array(0); }

    echo html_select_multiple($field->element_name, $items, $value, array('id'=>$field->id));

?>
