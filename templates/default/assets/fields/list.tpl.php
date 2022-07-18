<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    if($field->data['is_multiple']){

        echo html_select_multiple($field->element_name, $field->data['items'], $value, $field->data['dom_attr'], $field->data['is_tree']); ?>

        <?php if($field->data['multiple_select_deselect']){ ?>
            <div class="select_deselect">
                <a href="#" onclick="$('#<?php echo $field->element_name; ?> input:checkbox').prop('checked', true); return false;">
                    <?php echo LANG_SELECT_ALL; ?>
                </a>
                <a href="#" onclick="$('#<?php echo $field->element_name; ?> input:checkbox').prop('checked', false); return false;">
                    <?php echo LANG_DESELECT_ALL; ?>
                </a>
            </div>
        <?php } ?>

    <?php
    } elseif($field->data['is_chosen_multiple'] && !$field->native_tag) {

        $this->addTplJSNameFromContext('jquery-chosen');
        $this->addTplCSSNameFromContext('jquery-chosen');

        echo html_select($field->element_name, $field->data['items'], $value, ($field->data['dom_attr'] + array('multiple' => true)));

    } else {

        if (!$field->native_tag) {
            $this->addTplJSNameFromContext('jquery-chosen');
            $this->addTplCSSNameFromContext('jquery-chosen');
        }

        echo html_select($field->element_name, $field->data['items'], $value, $field->data['dom_attr']);

    }

?>
<script>
    <?php if ($field->data['parent']) { ?>
        $('#<?php echo str_replace(':', '_', $field->data['parent']['list']); ?>').on('change', function(){
            icms.forms.updateChildList('<?php echo $field->id; ?>', '<?php echo $field->data['parent']['url']; ?>', $(this).val(), <?php if (!is_array($value)) { ?>"<?php html($value); ?>"<?php } else { ?><?php echo json_encode($value); ?><?php } ?>, <?php if (!empty($field->data['parent']['filter_field_name'])) { ?>"<?php html($field->data['parent']['filter_field_name']); ?>"<?php } else { ?>$(this).attr('name')<?php } ?>);
        });
    <?php } ?>
    <?php if (!$field->native_tag && ($field->data['is_chosen_multiple'] || !$field->data['is_multiple'])) { ?>
        $('#<?php echo $field->data['dom_attr']['id']; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false});
    <?php } ?>
</script>