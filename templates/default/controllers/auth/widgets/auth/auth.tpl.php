<div class="widget_auth">
    <?php
        $this->renderForm($form, [], array(
            'action' => href_to('auth', 'login'),
            'method' => 'post',
            'cancel' => array(
                'show'  => true,
                'title' => LANG_REGISTRATION,
                'href'  => href_to('auth', 'register')),
            'submit' => array(
                'title' => LANG_LOG_IN
            )
        ), false);
    ?>
    <?php if($hooks_html){ ?>
        <div class="widget_log_in_openid">
            <div class="widget_log_in_openid_title"><?php echo LANG_LOG_IN_OPENID; ?></div>
            <?php echo html_each($hooks_html); ?>
        </div>
    <?php } ?>
</div>