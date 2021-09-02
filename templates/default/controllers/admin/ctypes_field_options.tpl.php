<?php
/**
 * Это устаревший файл, оставленный для совместимости
 * в CMS не используется
 */
if($options) {
    $visible_depend = array();
    ?>
    <?php foreach($options as $field) {

            if (is_array($values) && @array_key_exists($field->name, $values)){
                $value = $values[ $field->name ];
            } else {
                $value = $field->getDefaultValue();
            }

            $name = "options:{$field->name}";

            $field->setName($name);

            $styles = array();

            if (isset($field->is_visible)){
                if (!$field->is_visible){
                    $styles[] = 'display:none';
                }
            }

            $classes = array(
                'field',
                'ft_'.strtolower(substr(get_class($field), 5))
            );

            if($field->getOption('is_required')){ $classes[] = 'reguired_field'; }

            if($field->visible_depend){
                $visible_depend[] = $field;
                $classes[] = 'child_field';
            }

        ?>

        <div class="<?php echo implode(' ', $classes); ?>" id="f_<?php echo $field->id; ?>" <?php if ($styles) { ?>style="<?php echo implode(';', $styles); ?>"<?php } ?>>
                <?php echo $field->getInput($value); ?>
                <?php if(!empty($field->hint)) { ?><div class="hint"><?php echo $field->hint; ?></div><?php } ?>
        </div>
    <?php } ?>
<?php } ?>
<script>
    var id_name = $('#fset_visibility > .field').length === 1 ? '#tab-visibility' : '#f_is_in_filter, #tab-filter_access';

    <?php if(!$is_can_in_filter){ ?>
        $(id_name).hide(); $('#is_in_filter').prop('checked', false);
    <?php } else { ?>
        $(id_name).show();
    <?php } ?>
    <?php if(!$options){ ?>
        if ($('#f_type > input[id="type"]').length !== 0){
            $('#tab-type').hide();
        }
    <?php } ?>
    <?php if(!empty($visible_depend)){ foreach($visible_depend as $field){ ?>
        icms.forms.addVisibleDepend($('#f_<?php echo $field->id; ?>').closest('form').attr('id'), '<?php echo $field->name; ?>', <?php echo json_encode($field->visible_depend); ?>);
        <?php } ?>
        icms.forms.VDReInit();
    <?php } ?>
</script>