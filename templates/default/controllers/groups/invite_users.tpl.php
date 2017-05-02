<div id="invite_users_form" class="modal_padding">
<?php
    $this->renderForm($form, $data, array(
        'action'  => $this->href_to('invite_users', $group['id']),
        'submit'  => array('title' => LANG_INVITE),
        'method'  => 'ajax',
        'toolbar' => false
    ), $errors);
?>
</div>
<script type="text/javascript">
    $(function(){
        $('.chosen-container-multi .chosen-choices li.search-field input[type="text"]').width(150);
    });
    icms.modal.setCallback('open', function (){
        setTimeout(function(){ $('.nyroModalCont').css('overflow', 'visible'); }, 300);
    });
    icms.modal.setCallback('close', function(){
        icms.forms.form_changed = false;
    });
    function inviteFormSuccess (form_data, result){
        icms.modal.alert(result.text);
    }
</script>