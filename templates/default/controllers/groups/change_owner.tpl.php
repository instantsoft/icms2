<div class="modal_padding">
<?php
    $this->renderForm($form, $data, array(
        'action' => $form_action,
        'method' => 'ajax',
        'submit' => array(
            'title' => LANG_CONTINUE
        )
    ), $errors);
?>
</div>