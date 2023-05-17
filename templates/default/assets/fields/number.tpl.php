<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php if($field->data['prefix']){ ?><span class="input-number-prefix"><?php echo $field->data['prefix']; ?></span><?php } ?>
<?php echo html_input($field->data['type'], $field->element_name, $value, $field->data['attributes']); ?>
<?php if($field->data['units']){ ?><span class="input-number-units"><?php echo $field->data['units']; ?></span><?php } ?>