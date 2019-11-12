<?php if ($field->title) { ?><label class="d-block" for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="d-flex align-items-center">
    <span class="mr-2"><?php echo LANG_FROM; ?></span>
    <?php echo html_datepicker($field->element_name.'[from]', $from, array('id' => $field->id.'_from')).' '; ?>
    <span class="mx-2"><?php echo LANG_TO; ?></span>
    <?php echo html_datepicker($field->element_name.'[to]', $to, array('id' => $field->id.'_to')); ?>
</div>



