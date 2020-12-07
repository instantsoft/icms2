<div class="custom-control custom-switch">
    <?php echo html_checkbox($field->element_name, (bool)$value, 1, array('id'=>$field->id, 'class' => 'custom-control-input')); ?>
    <label class="custom-control-label" for="<?php echo $field->id; ?>">
        <?php if ($field->title) { ?>
            <?php echo $field->title; ?>
        <?php } ?>
    </label>
</div>
<?php if(!empty($field->toggle)) { ?>
<?php ob_start(); ?>
    <?php $self_id = "#f_{$field->id}" ;?>
    <script>
        $(document).ready(function(){
            $('<?php echo $self_id; ?> .input-checkbox').on('change', function(){
                <?php foreach($field->toggle as $target) { ?>
                    $('#f_<?php echo $target; ?>').toggle();
                <?php } ?>
            });
            var state = $('<?php echo $self_id; ?> .input-checkbox').prop('checked');
            <?php foreach($field->toggle as $target) { ?>
               if(state) { $('#f_<?php echo $target; ?>').show(); }
            <?php } ?>
        });
    </script>
<?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>

