<?php $this->addJS('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSS('templates/default/css/jquery-ui.css'); ?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo LANG_FROM . ' ' . html_datepicker($field->element_name."[from]", $from, array('id'=>$field->id.'_from')); ?> 
<?php echo LANG_TO . ' ' . html_datepicker($field->element_name."[to]", $to, array('id'=>$field->id.'_to')); ?>
