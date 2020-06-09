<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="input-group">
    <?php if($field->data['prefix']){ ?>
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo $field->data['prefix']; ?></span>
        </div>
    <?php } ?>
    <?php echo html_input('text', $field->element_name, $value, array('id'=>$field->id, 'size'=>5, 'class'=>'input-number', 'required'=>(array_search(array('required'), $field->getRules()) !== false))); ?>
    <?php if($field->data['units']){ ?>
        <div class="input-group-append">
            <span class="input-group-text input-number-units"><?php echo $field->data['units']; ?></span>
        </div>
    <?php } ?>
</div>