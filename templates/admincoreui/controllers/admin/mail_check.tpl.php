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
        if(result.type === 'ui_error'){
            toastr.error(result.text);
        } else {
            icms.modal.close();
            toastr.success(result.text);
        }
    }

</script>
