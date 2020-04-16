<div class="modal_padding">
<?php
    $this->renderForm($form, array(), array(
        'action' => $form_action,
        'method' => 'ajax',
        'submit' => array(
            'title' => LANG_SEND
        )
    ), array());
?>
</div>