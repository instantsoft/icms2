<div class="width_320">
<?php
    $this->renderForm($form, $mod, array(
        'action' => href_to('admin', 'controllers', array('edit', 'moderation', 'edit_trash_left_time', $mod['id'])),
        'method' => 'ajax'
    ), $errors); ?>
</div>
<script>
    function leftTimeSuccess (form_data, result){
        if(result.trash_left_time){
            $('#moderator-'+result.id+' .trash_left_time_num').html(result.trash_left_time);
        }
        icms.modal.close();
    }
</script>