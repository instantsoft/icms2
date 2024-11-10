<div id="mail_check">
    <?php $this->renderForm($form, $values, [
        'action' => $this->href_to('settings', ['mail_check']),
        'submit' => [
            'title' => LANG_SEND
        ],
        'method' => 'ajax',
    ], $errors); ?>
</div>

<script nonce="<?php echo $this->nonce; ?>">
    function checkSuccess(form_data, result){
        if(result.type === 'ui_error'){
            toastr.error(result.text);
        } else {
            icms.modal.close();
            toastr.success(result.text);
        }
    }
</script>