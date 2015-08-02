<?php $this->addJS('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSS('templates/default/css/jquery-ui.css'); ?>

<?php if($field->title){ ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_datepicker($field->data['fname_date'], $field->data['date'], array('id'=>$field->id)); ?>

<?php if($field->data['show_time']){ ?>
    <?php echo html_select_range($field->data['fname_hour'], 0, 23, 1, true, $field->data['hours']); ?> :
    <?php echo html_select_range($field->data['fname_min'], 0, 59, 5, true, $field->data['mins']); ?>
<?php } ?>
