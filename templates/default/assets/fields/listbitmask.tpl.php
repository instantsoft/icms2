<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_select_multiple($field->element_name, $field->data['items'], $field->data['selected'], array('id'=>$field->id)); ?>
