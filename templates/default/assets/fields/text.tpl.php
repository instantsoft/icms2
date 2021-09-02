<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_textarea($field->element_name, $value, $field->data['attributes']); ?>
<?php if($field->getOption('show_symbol_count')){ ?>
<script>
$(function(){
    icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo ($field->getOption('max_length') ? (int)$field->getOption('max_length') : 0) ?>, <?php echo ($field->getOption('min_length') ? (int)$field->getOption('min_length') : 0) ?>);
});
</script>
<?php }
