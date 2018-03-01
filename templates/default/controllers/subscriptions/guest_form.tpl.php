<div class="modal_padding">
<?php
    $this->renderForm($form, array(), array(
        'action' => $this->href_to('subscribe'),
        'params' => $params,
        'method' => 'ajax',
        'submit' => array(
            'title' => LANG_SBSCR_SUBSCRIBE
        )
    ), false);
?>
</div>