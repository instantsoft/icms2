<div class="custom-control custom-switch">
    <?php echo html_checkbox($field->element_name, (bool)$value, 1, $field->data['attributes']); ?>
    <label class="custom-control-label" for="<?php echo $field->id; ?>">
        <?php if ($field->title) { ?>
            <span><?php echo $field->title; ?></span>
        <?php } ?>
    </label>
</div>

