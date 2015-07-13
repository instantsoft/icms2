<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id, 'size'=>5, 'class'=>'input-number')); ?>
<?php $units = $field->getProperty('units'); ?>
<?php if(!$units) { $units = $field->getOption('units'); } ?>
<?php if ($units) { ?><span class="input-number-units"><?php html($units); ?></span><?php } ?>
