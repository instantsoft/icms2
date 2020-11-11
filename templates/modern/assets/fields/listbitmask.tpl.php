<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php if($field->getOption('is_checkbox_multiple')){ ?>

    <div class="input_checkbox_list" id="<?php echo $field->id; ?>">
        <?php foreach ($field->data['items'] as $v => $title){ ?>
            <?php $checked = in_array($v, $field->data['selected'], true); $ch_id = $field->id.$v; ?>
            <div class="custom-control custom-checkbox custom-control-inline">
                <input name="<?php echo $field->element_name; ?>[]" value="<?php html($v); ?>" type="checkbox" class="custom-control-input" id="<?php echo $ch_id; ?>"<?php if($checked) { ?>checked<?php } ?>>
                <label class="custom-control-label" for="<?php echo $ch_id; ?>">
                    <?php html($title); ?>
                </label>
            </div>
        <?php } ?>
    </div>

<?php } else { ?>

    <?php
        $this->addTplJSNameFromContext('jquery-chosen');
        $this->addTplCSSNameFromContext('jquery-chosen');
    ?>

    <?php echo html_select($field->element_name, $field->data['items'], $field->data['selected'], (array('id'=>$field->id, 'multiple' => true))); ?>
    <?php ob_start(); ?>
        <script>
            $('#<?php echo $field->id; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', width: '100%', search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
            $(function(){
                $('.chosen-container-multi .chosen-choices li.search-field input[type="text"]').width(150);
            });
        </script>
    <?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>