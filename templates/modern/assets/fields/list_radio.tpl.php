<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div <?php echo html_attr_str($field->data['dom_attr']); ?>>
    <?php foreach ($field->data['items'] as $key => $title) { ?>
        <div class="custom-control custom-radio">
            <?php
                $checked = ((string) $value === (string) $key) ? 'checked' : '';
                $id = 'radio-'.$field->id.$key;
            ?>
            <input type="radio" <?php echo $checked; ?> id="<?php html($id); ?>" name="<?php echo $field->element_name; ?>" value="<?php html($key); ?>" class="custom-control-input">
            <label class="custom-control-label" for="<?php html($id); ?>">
                <?php echo $title; ?>
            </label>
        </div>
    <?php } ?>
</div>