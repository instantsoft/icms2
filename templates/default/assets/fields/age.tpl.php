<?php $this->addJSFromContext('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSSFromContext('templates/default/css/jquery-ui.css'); ?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->getOption('date_title'); ?></label><?php } ?>

<?php echo html_datepicker($field->element_name, $field->data['date'], array('id'=>$field->id)); ?>
