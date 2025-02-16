<?php
    $this->renderForm($form, $mod, [
        'action' => href_to('admin', 'controllers', ['edit', 'moderation', 'edit_trash_left_time', $mod['id']]),
        'method' => 'ajax'
    ], $errors);
?>

<script nonce="<?php echo $this->nonce; ?>">
    function leftTimeSuccess (form_data, result){
        if(result.trash_left_time){
            $('#moderator-'+result.id+' .trash_left_time_num').html(result.trash_left_time);
        }
        icms.modal.close();
    }
</script>