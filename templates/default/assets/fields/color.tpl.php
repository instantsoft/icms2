<?php
    $this->addJS('templates/default/js/colorpicker.js');
    $this->addCSS('templates/default/css/colorpicker.css');
?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id)); ?>

<script>$('input#<?php echo $field->id; ?>').minicolors();</script>
