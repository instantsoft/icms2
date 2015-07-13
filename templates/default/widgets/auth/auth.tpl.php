<div class="widget_auth">
    <form action="<?php echo href_to('auth', 'login'); ?>" method="POST">

        <?php echo html_input('hidden', 'is_back', 1); ?>
        
        <div class="field">
            <label><?php echo LANG_EMAIL; ?>:</label>
            <a href="<?php echo href_to('auth', 'register'); ?>"><?php echo LANG_REGISTRATION; ?></a>
            <?php echo html_input('text', 'login_email'); ?>
        </div>

        <div class="field">
            <label><?php echo LANG_PASSWORD; ?>:</label>
            <a href="<?php echo href_to('auth', 'restore'); ?>"><?php echo LANG_FORGOT_PASS; ?></a>
            <?php echo html_input('password', 'login_password'); ?>
        </div>

        <div class="options">
            <input type="checkbox" id="remember" name="remember" value="1" />
            <label for="remember">
                <?php echo LANG_REMEMBER_ME; ?>
            </label>
        </div>
        
        <div class="buttons">
            <?php echo html_submit(LANG_LOG_IN); ?>
        </div>

    </form>
</div>
