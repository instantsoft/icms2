<?php $this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
]); ?>
<?php $this->addTplCSSNameFromContext('jquery-ui'); ?>
<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<div class="slider-range-wrap" id="slider-range-<?php echo $field->id; ?>"></div>
<div class="slider-range-hint">
<?php if($field->getOption('filter_range_show_input')){ ?>
    <?php echo LANG_FROM; ?>
    <?php echo html_input($field->data['type'], $field->element_name.'[from]', $field->data['slide_params']['values'][0], ['class'=>'input-small', 'id' => $field->id.'_from', 'step' => 'any']); ?>
    <?php echo LANG_TO; ?>
    <?php echo html_input($field->data['type'], $field->element_name.'[to]', $field->data['slide_params']['values'][1], ['class'=>'input-small', 'id' => $field->id.'_to', 'step' => 'any']); ?>
    <?php if($field->data['units']){ ?><span class="input-number-units"><?php echo $field->data['units']; ?></span><?php } ?>
<?php } else { ?>
    <?php echo LANG_FROM; ?>
    <span class="slider-range-from"><?php echo $field->data['slide_params']['values'][0]; ?></span>
    <?php echo LANG_TO; ?>
    <span class="slider-range-to"><?php echo $field->data['slide_params']['values'][1]; ?></span>
    <?php if($field->data['units']){ ?><span class="input-number-units"><?php echo $field->data['units']; ?></span><?php } ?>

    <?php echo html_input('hidden', $field->element_name.'[from]', $from, ['id' => $field->id.'_from']); ?>
    <?php echo html_input('hidden', $field->element_name.'[to]', $to, ['id' => $field->id.'_to']); ?>
<?php } ?>
</div>

<?php ob_start(); ?>
    <script>
    $(function(){
        $("#slider-range-<?php echo $field->id; ?>").slider($.extend(<?php echo json_encode($field->data['slide_params'], JSON_NUMERIC_CHECK); ?>, {
            range: true,
            slide: function( event, ui ) {
                var hint = $(ui.handle).closest('.slider-range-wrap').next();
                $(hint).find('.slider-range-from').text(ui.values[0]);
                $('#<?php echo $field->id.'_from'; ?>').val(ui.values[0]).triggerHandler('input');
                $(hint).find('.slider-range-to').text(ui.values[1]);
                $('#<?php echo $field->id.'_to'; ?>').val(ui.values[1]).triggerHandler('input');
            }
        }));
        $('#<?php echo $field->id.'_from'; ?>.input-small').on('keyup', function (){
            $("#slider-range-<?php echo $field->id; ?>").slider('values', 0, $(this).val());
        });
        $('#<?php echo $field->id.'_to'; ?>.input-small').on('keyup', function (){
            $("#slider-range-<?php echo $field->id; ?>").slider('values', 1, $(this).val());
        });
    });
    </script>
<?php $this->addBottom(ob_get_clean()); ?>