<div class="widget_auth">
    <form action="<?php echo href_to('auth', 'login'); ?>" method="POST">

        <div class="field">
            <label><?php echo LANG_EMAIL; ?>:</label>
            <a href="<?php echo href_to('auth', 'register'); ?>" tabindex="6"><?php echo LANG_REGISTRATION; ?></a>
            <?php echo html_input('text', 'login_email', '', array('required'=>true, 'tabindex' => '1')); ?>
        </div>

        <div class="field">
            <label><?php echo LANG_PASSWORD; ?>:</label>
            <a href="<?php echo href_to('auth', 'restore'); ?>" tabindex="5"><?php echo LANG_FORGOT_PASS; ?></a>
            <?php echo html_input('password', 'login_password', '', array('required'=>true, 'tabindex' => '2')); ?>
        </div>

        <div class="options">
            <input type="checkbox" id="remember" name="remember" value="1" tabindex="3" />
            <label for="remember">
                <?php echo LANG_REMEMBER_ME; ?>
            </label>
        </div>

        <div class="buttons">
            <?php echo html_submit(LANG_LOG_IN, 'submit', array('tabindex' => '4')); ?>
        </div>

    </form>
</div>