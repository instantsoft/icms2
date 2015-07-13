<?php $this->addJS('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSS('templates/default/css/jquery-ui.css'); ?>

<?php

    $config = cmsConfig::getInstance();
    $is_show_time = $field->getOption('show_time');

    $date = $value ? date($config->date_format, strtotime($value)) : '';

    if ($is_show_time){
        list($hours, $mins) = explode(':', date('H:i', strtotime($value)));
    }

    $date_field_name = $is_show_time ? $field->element_name  . '[date]' : $field->element_name;
    $hours_field_name = $field->element_name . '[hour]';
    $mins_field_name = $field->element_name . '[min]';

?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_datepicker($date_field_name, $date, array('id'=>$field->id)); ?>

<?php if ($is_show_time) { ?>
    <?php echo html_select_range($hours_field_name, 0, 23, 1, true, $hours); ?> :
    <?php echo html_select_range($mins_field_name, 0, 59, 5, true, $mins); ?>
<?php } ?>
