<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_wysiwyg($field->element_name, $value, $field->getOption('editor'), $field->getOption('editor_options', array()));
