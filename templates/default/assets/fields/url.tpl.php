<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id, 'required'=>(array_search(array('required'), $field->getRules()) !== false))); ?>
<?php if($field->getOption('title')){ echo "<div class='hint'>".LANG_PARSER_URL_TITLE_HINT."</div>"; }