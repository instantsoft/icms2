<?php
    $this->addTplJSNameFromContext('colorpicker');
    $this->addTplCSSNameFromContext('colorpicker');
?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo html_input(($field->getOption('control_type')=='swatches' ? 'hidden' : 'text'), $field->element_name, $value, array('id'=>$field->id, 'autocomplete' => 'off')); ?>

<?php ob_start(); ?>
<script>
    $(function(){
        var minicolors_options = <?php echo json_encode($field->data['minicolors_options']); ?>;
        <?php if($field->getOption('control_type')=='swatches'){ ?>
            minicolors_options.change = function(value, opacity) {
                $(this).minicolors('hide');
            };
        <?php } ?>
        $('input#<?php echo $field->id; ?>').minicolors(minicolors_options);
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>