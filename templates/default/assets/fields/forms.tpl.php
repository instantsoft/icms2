<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    $this->addTplJSNameFromContext('jquery-chosen');
    $this->addTplCSSNameFromContext('jquery-chosen');

    echo html_select($field->element_name, $field->data['items'], $value, ['id' => $field->id]);
?>
<?php ob_start(); ?>
<script>
    $('#<?php echo $field->id; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false});
</script>
<?php $this->addBottom(ob_get_clean()); ?>