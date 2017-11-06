<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_textarea($field->element_name, $value, array('rows'=>$field->getOption('size'), 'id'=>$field->id, 'required'=>(array_search(array('required'), $field->getRules()) !== false))); ?>
<?php if($field->getOption('show_symbol_count')){ ?>
<script type="text/javascript">
$(function(){
    icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo ($field->getOption('max_length') ? (int)$field->getOption('max_length') : 0) ?>, <?php echo ($field->getOption('min_length') ? (int)$field->getOption('min_length') : 0) ?>);
});
</script>
<?php } ?>