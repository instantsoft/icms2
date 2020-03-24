<?php
    $this->addTplJSNameFromContext('colorpicker');
    $this->addTplCSSNameFromContext('colorpicker');
?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_input(($field->getOption('control_type')=='swatches' ? 'hidden' : 'text'), $field->element_name, $value, array('id'=>$field->id, 'autocomplete' => 'off')); ?>

<script type="text/javascript">
    $('input#<?php echo $field->id; ?>').minicolors({
        swatches: <?php echo json_encode($field->getOption('swatches')); ?>,
        <?php if($field->getOption('control_type')=='swatches'){ ?>
            change: function(value, opacity) {
                $(this).minicolors('hide');
            },
        <?php } ?>
        control: '<?php echo $field->getOption('control_type', 'hue'); ?>'
    });
</script>