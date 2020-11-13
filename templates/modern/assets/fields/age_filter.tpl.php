<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="d-flex align-items-center">
    <div class="input-group mr-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_FROM; ?></span>
        </div>
        <?php echo html_input('text', $field->element_name.'[from]', $from, array('class'=>'input-small')); ?>
    </div>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_TO; ?></span>
        </div>
        <?php echo html_input('text', $field->element_name.'[to]', $to, array('class'=>'input-small')); ?>
        <div class="input-group-append">
            <span class="input-group-text input-number-units"><?php echo $range; ?></span>
        </div>
    </div>
</div>