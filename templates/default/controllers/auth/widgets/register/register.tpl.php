<?php
    $this->renderForm($form, array(), array(
        'action'      => href_to('auth', 'register'),
        'method'      => 'post',
        'append_html' => $captcha_html,
        'submit'      => array(
            'title' => LANG_CONTINUE
        )
    ), false);
