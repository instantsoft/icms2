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
                <?php if(!empty($field->hint)) { ?><div class="hint">&mdash; <?php echo $field->hint; ?></div><?php } ?>
        </div>
    <?php } ?>
<?php } ?>
<script type="text/javascript">
    if($('#fset_visibility > .field').length == 1){
        id_name = '#tab-visibility';
    } else {
        id_name = '#f_is_in_filter';
    }
    <?php if(!$is_can_in_filter){ ?>
        $(id_name).hide(); $('#is_in_filter').prop('checked', false)
    <?php } else { ?>
        $(id_name).show();
    <?php } ?>
</script>
