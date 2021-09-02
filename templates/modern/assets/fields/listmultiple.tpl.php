<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<div class="input_checkbox_list" id="<?php echo $field->id; ?>">
    <?php foreach ($field->data['items'] as $v => $title){ ?>
        <?php $checked = in_array($v, $value, true); $ch_id = $field->id.$v; ?>
        <div class="custom-control custom-checkbox<?php if($field->is_vertical){ ?> mb-1<?php } else { ?> custom-control-inline<?php } ?>">
            <input name="<?php echo $field->element_name; ?>[]" value="<?php html($v); ?>" type="checkbox" class="custom-control-input" id="<?php echo $ch_id; ?>"<?php if($checked) { ?>checked<?php } ?>>
            <label class="custom-control-label" for="<?php echo $ch_id; ?>">
                <?php html($title); ?>
            </label>
        </div>
    <?php } ?>
</div>

<?php if($field->getProperty('show_all')){ ob_start(); ?>
<script>
    $(function() {
        $('#<?php echo $field->id; ?> input').on('click', function (){
            var v = $(this).val();
            var p = $(this).closest('.input_checkbox_list');
            if(v==0){
                $('input', p).not('input[value="0"]').prop('checked', false);
            } else {
                $('input[value="0"]', p).prop('checked', false);
            }
        });
    });
</script>
<?php $this->addBottom(ob_get_clean()); } ?>
