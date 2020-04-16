<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>" class="d-block"><?php echo $field->title; ?></label><?php } ?>
<div class="d-flex align-items-center">
    <span class="mr-2"><?php echo LANG_FROM; ?></span>
    <?php echo html_input('text', $field->element_name.'[from]', $from, array('class'=>'short-input')); ?>
    <span class="mx-2"><?php echo LANG_TO; ?></span>
    <div class="input-group">
        <?php echo html_input('text', $field->element_name.'[to]', $to, array('class'=>'short-input')); ?>
        <?php if($field->data['units']){ ?>
            <div class="input-group-append">
                <span class="input-group-text input-number-units"><?php echo $field->data['units']; ?></span>
            </div>
        <?php } ?>
    </div>
</div>