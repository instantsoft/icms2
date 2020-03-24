<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo LANG_FROM . ' ' . html_input('text', $field->element_name.'[from]', $from, array('class'=>'input-small')).' '; ?>
<?php echo LANG_TO . ' ' . html_input('text', $field->element_name.'[to]', $to, array('class'=>'input-small')).' '; ?>
<?php echo $range; ?>