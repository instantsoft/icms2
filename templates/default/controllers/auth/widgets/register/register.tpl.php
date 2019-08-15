<?php
    $this->renderForm($form, array(), array(
        'action'      => href_to('auth', 'register'),
        'method'      => 'post',
        'submit'      => array(
            'title' => LANG_CONTINUE
        )
    ), false);
