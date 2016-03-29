<?php
    $this->addJSFromContext('templates/default/js/colorpicker.js');
    $this->addCSSFromContext('templates/default/css/colorpicker.css');
?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id, 'autocomplete' => 'off')); ?>

<script>$('input#<?php echo $field->id; ?>').minicolors({swatches: ['#fff','#000','#f00','#0f0','#00f','#ff0','#0ff']});</script>
