<div id="mail_check">
    <?php $this->renderForm($form, $values, array(
        'action' => $this->href_to('settings', array('mail_check')),
        'submit' => array(
            'title' => LANG_SEND
        ),
        'method' => 'ajax',
    ), $errors); ?>
</div>

<script>

    function checkSuccess(form_data, result){
        icms.modal.alert(result.text, result.type);
    }

    icms.modal.setCallback('close', function(){
        icms.forms.form_changed = false;
    });

</script>