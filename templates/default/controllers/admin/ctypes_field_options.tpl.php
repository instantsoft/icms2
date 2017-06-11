<?php if($options) { ?>
    <?php foreach($options as $field) { ?>

        <?php

            if (is_array($values) && @array_key_exists($field->name, $values)){
                $value = $values[ $field->name ];
            } else {
                $value = $field->getDefaultValue();
            }

            $name = "options:{$field->name}";

            $field->setName($name);

        ?>

        <div class="field" id="f_<?php echo $field->id; ?>">
                <?php echo $field->getInput($value); ?>
                <?php if(!empty($field->hint)) { ?><div class="hint"><?php echo $field->hint; ?></div><?php } ?>
        </div>
    <?php } ?>
<?php } ?>
<script type="text/javascript">
    if($('#fset_visibility > .field').length == 1){
        var id_name = '#tab-visibility';
    } else {
        var id_name = '#f_is_in_filter, #tab-filter_access';
    }
    <?php if(!$is_can_in_filter){ ?>
        $(id_name).hide(); $('#is_in_filter').prop('checked', false);
    <?php } else { ?>
        $(id_name).show();
    <?php } ?>
    <?php if(!$options){ ?>
        if ($('#f_type > input[id=type]').length != 0){
            $('#tab-type').hide();
        }
    <?php } ?>
</script>