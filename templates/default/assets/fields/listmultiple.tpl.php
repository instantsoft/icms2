<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_select_multiple($field->element_name, $field->data['items'], $value, array('id'=>$field->id, 'class' => $field->is_vertical ? 'block_labels' : '')); ?>

<?php if($field->getProperty('show_all')){ ?>

    <script>
        $(function() {
            $('#<?php echo $field->id; ?> input').on('click', function (){
                v = $(this).val();
                p = $(this).parents('.input_checkbox_list');
                if(v==0){
                    $('input', p).not('input[value="0"]').prop('checked', false);
                } else {
                    $('input[value="0"]', p).prop('checked', false);
                }
            });
        });
    </script>

<?php }