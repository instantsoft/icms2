<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    if($field->data['is_multiple']){ ?>

        <div class="input_checkbox_list" id="<?php echo $field->id; ?>">
            <?php foreach ($field->data['items'] as $v => $title){ ?>
                <?php
                $checked = $value && in_array($v, $value, true);
                $ch_id = $field->id.$v;
                $level = substr_count($title, '-')-1;
                $level = $level<0 ? 0 : $level;
                ?>
                <div class="custom-control custom-checkbox mb-1">
                    <input name="<?php echo $field->element_name; ?>[]" value="<?php html($v); ?>" type="checkbox" class="custom-control-input" id="<?php echo $ch_id; ?>"<?php if($checked) { ?>checked<?php } ?>>
                    <label class="custom-control-label" for="<?php echo $ch_id; ?>">
                        <span style="margin-left: <?php echo $level*0.75; ?>rem"><?php html(ltrim($title, '- ')); ?></span>
                    </label>
                </div>
            <?php } ?>
        </div>

        <?php if($field->data['multiple_select_deselect']){ ?>
            <div class="select_deselect mt-2">
                <a href="#" onclick="$('#<?php echo $field->id; ?> input:checkbox').prop('checked', true); return false;">
                    <?php echo LANG_SELECT_ALL; ?>
                </a>
                <a class="text-muted" href="#" onclick="$('#<?php echo $field->id; ?> input:checkbox').prop('checked', false); return false;">
                    <?php echo LANG_DESELECT_ALL; ?>
                </a>
            </div>
        <?php } ?>

    <?php } elseif($field->data['is_chosen_multiple'] && !$field->native_tag) {

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
<?php ob_start(); ?>
<script>
    <?php if ($field->data['parent']) { ?>
        $('#<?php echo str_replace(':', '_', $field->data['parent']['list']); ?>').on('change', function(){
            icms.forms.updateChildList('<?php echo $field->id; ?>', '<?php echo $field->data['parent']['url']; ?>', $(this).val(), <?php if (!is_array($value)) { ?>"<?php html($value); ?>"<?php } else { ?><?php echo json_encode($value); ?><?php } ?>, <?php if (!empty($field->data['parent']['filter_field_name'])) { ?>"<?php html($field->data['parent']['filter_field_name']); ?>"<?php } else { ?>$(this).attr('name')<?php } ?>);
        });
    <?php } ?>
    <?php if (!$field->native_tag && ($field->data['is_chosen_multiple'] || !$field->data['is_multiple'])) { ?>
        $('#<?php echo $field->data['dom_attr']['id']; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo $field->data['select_hint_if_empty']; ?>', placeholder_text_multiple: '<?php echo $field->data['select_hintmp_if_empty']; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false});
        <?php if (!empty($field->data['dom_attr']['readonly'])) { ?>
            $('#<?php echo $field->data['dom_attr']['id']; ?>').prop('disabled',true).trigger('chosen:updated').prop('disabled',false);
        <?php } ?>
    <?php } ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>