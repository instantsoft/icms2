<?php $this->addJS('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSS('templates/default/css/jquery-ui.css'); ?>

<?php

    $config = cmsConfig::getInstance();
    $date = $value ? date($config->date_format, strtotime($value)) : '';

?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->getOption('date_title'); ?></label><?php } ?>

<?php echo html_datepicker($field->element_name, $date, array('id'=>$field->id)); ?>
