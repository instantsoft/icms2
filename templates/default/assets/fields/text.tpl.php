<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_textarea($field->element_name, $value, array('rows'=>$field->getOption('size'), 'id'=>$field->id)); ?>
