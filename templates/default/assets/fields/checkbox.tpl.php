<?php if($field->title){ ?><label><?php } ?>
    <?php echo html_checkbox($field->element_name, (bool)$value, 1, $field->data['attributes']); ?>
    <?php if($field->title){echo $field->title;} ?>
    <?php if(!empty($field->toggle)) { ?>
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
    <?php } ?>
<?php if($field->title){ ?></label><?php } ?>
