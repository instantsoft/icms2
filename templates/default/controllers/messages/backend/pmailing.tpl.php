<?php

    $this->setPageTitle(LANG_PM_PMAILING);
    $this->addBreadcrumb(LANG_PM_PMAILING);

    $this->renderForm($form, $mailing, array(
        'action' => '',
        'method' => 'post',
        'submit' => array(
            'title' => LANG_SUBMIT
        )
    ), $errors);
?>
<script>
    $(function() {
        $('#type').on('change', function (){
            if($(this).val() == 'message'){
                $('#f_sender_user_email').show();
            } else {
                $('#f_sender_user_email').hide();
            }
        }).triggerHandler('change');
    });
</script>