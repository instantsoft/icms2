<div id="invite_users_form" class="modal_padding">
<?php
    $this->renderForm($form, $data, array(
        'action'  => $this->href_to('set_roles', array($group['id'], $user_id, 1)),
        'method'  => 'ajax',
        'toolbar' => false
    ), $errors);
?>
</div>
<script>
    icms.modal.setCallback('close', function(){
        icms.forms.form_changed = false;
    });
    function roleFormSuccess (form_data, result){
        icms.modal.alert(result.text);
    }
</script>