<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php if ($item) { echo $item['title']; } ?>

<?php echo html_input('hidden', $field->element_name, $value, array('id'=>$field->id)); ?>
