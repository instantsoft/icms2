<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_input('text', $field->element_name, $value, $field->data['attributes']); ?>
<?php if($field->getOption('show_symbol_count')){ ob_start(); ?>
<script>
$(function(){
    icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo intval($field->getOption('max_length', 0)); ?>, <?php echo intval($field->getOption('min_length', 0)); ?>);
});
</script>
<?php $this->addBottom(ob_get_clean()); } ?>