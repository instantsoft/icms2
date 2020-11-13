<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php if($field->data['prefix']){ ?><span class="input-number-prefix"><?php echo $field->data['prefix']; ?></span><?php } ?>
<?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id, 'size'=>5, 'class'=>'input-number', 'required'=>(array_search(array('required'), $field->getRules()) !== false))); ?>
<?php if($field->data['units']){ ?><span class="input-number-units"><?php echo $field->data['units']; ?></span><?php } ?>