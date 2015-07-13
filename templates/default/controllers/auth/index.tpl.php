<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
    <tr>
        <td>

            <table cellpadding="0" cellspacing="0" border="0" width="70%" align="center">
                <tr>
                    <td colspan="2">
                        <h1><?php echo LANG_PLEASE_LOGIN; ?></h1>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <h2><?php echo LANG_REG_FIRST_TIME; ?></h2>
                    </td>
                    <td>
                        <h2><?php echo LANG_REG_ALREADY; ?></h2>
                    </td>
                </tr>
                <tr>
                    <td>

                        <form action="/auth/register" method="POST">

                            <table class="feedback_form" cellpadding="0" cellspacing="0" border="0">

                                <tr><td class="label" valign="top"><label><?php echo LANG_EMAIL; ?>:</label></td></tr>
                                <tr><td class="field"><?php echo html_input('text', 'reg_email'); ?></td></tr>

                                <tr><td class="label" valign="top"><label><?php echo LANG_PASSWORD; ?>:</label></td></tr>
                                <tr><td class="field"><?php echo html_input('password', 'reg_password'); ?></td></tr>

                                <tr><td class="label" valign="top"><label><?php echo LANG_RETYPE_PASSWORD; ?>:</label></td></tr>
                                <tr><td class="field"><?php echo html_input('password', 'reg_password2'); ?></td></tr>

                            </table>

                            <?php echo html_submit(LANG_REGISTRATION); ?>

                        </form>

                    </td>
                    <td>

                        <form action="/auth/login" method="POST">

                            <table class="feedback_form" cellpadding="0" cellspacing="0" border="0">

                                <tr><td class="label" valign="top"><label><?php echo LANG_EMAIL; ?>:</label></td></tr>
                                <tr><td class="field"><?php echo html_input('text', 'login_email'); ?></td></tr>

                                <tr><td class="label" valign="top"><label><?php echo LANG_PASSWORD; ?>:</label></td></tr>
                                <tr><td class="field"><?php echo html_input('password', 'login_password'); ?></td></tr>

                                <tr>
                                    <td style="padding:15px 0">
                                        <label>
                                            <input type="checkbox" name="remember" value="1" /> <?php echo LANG_REMEMBER_ME; ?>
                                        </label>
                                    </td>
                                </tr>

                            </table>

                            <?php echo html_submit(LANG_LOG_IN); ?>

                        </form>

                    </td>
                </tr>
            </table>


        </td>
    </tr>
</table>
