<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>" class="d-block"><?php echo $field->title; ?></label><?php } ?>
<div class="d-flex align-items-center">
    <div class="input-group mr-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_FROM; ?></span>
        </div>
        <?php echo html_input('text', $field->element_name.'[from]', $from, ['class'=>'short-input']); ?>
    </div>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_TO; ?></span>
        </div>
        <?php echo html_input('text', $field->element_name.'[to]', $to, array('class'=>'short-input')); ?>
        <?php if($field->data['units']){ ?>
            <div class="input-group-append">
                <span class="input-group-text input-number-units"><?php echo $field->data['units']; ?></span>
            </div>
        <?php } ?>
    </div>
</div>