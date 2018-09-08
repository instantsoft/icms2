<?php
    $this->setPageTitle(LANG_LOG_IN);
    $this->addBreadcrumb(LANG_LOG_IN);
    $is_ajax = $this->controller->request->isAjax();
?>

<?php if($is_ajax && $captcha_html){ ?>
    <script>window.location.href='<?php echo $this->href_to('login'); ?>';</script>
    <?php return; ?>
<?php } ?>

<?php if($is_ajax){ ?>
    <div style="padding: 20px">
<?php } else { ?>
    <h1><?php echo LANG_PLEASE_LOGIN; ?></h1>
<?php } ?>

<table class="login_layout">

    <tr>
        <td class="left_cell" valign="top">
            <form action="<?php echo $this->href_to('login'); ?>" method="POST">

                <?php if ($back_url){ echo html_input('hidden', 'back', $back_url); } ?>

                <div class="login_form">

                    <h3><?php echo LANG_LOG_IN_ACCOUNT; ?></h3>

                    <div class="label"><label><?php echo LANG_EMAIL; ?>:</label></div>
                    <div class="field"><?php echo html_input('text', 'login_email', '', array('id'=>'login_email', 'required'=>true, 'autofocus'=>true)); ?></div>

                    <div class="label"><label><?php echo LANG_PASSWORD; ?>:</label></div>
                    <div><?php echo html_input('password', 'login_password', '', array('required'=>true)); ?></div>

                    <div class="options">
                        <input type="checkbox" id="remember" name="remember" value="1" />
                        <label for="remember">
                            <?php echo LANG_REMEMBER_ME; ?> |
                            <a href="<?php echo $this->href_to('restore'); ?>"><?php echo LANG_FORGOT_PASS; ?></a>
                        </label>

                    </div>

                    <?php echo $captcha_html; ?>

                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <?php echo html_submit(LANG_LOG_IN); ?>
                            </td>
                            <td class="reg_link">
                                <?php echo LANG_NO_ACCOUNT; ?> <a href="<?php echo $this->href_to('register').($back_url ? '?back='.$back_url : ''); ?>"><?php echo LANG_REGISTRATION; ?></a>
                            </td>
                        </tr>
                    </table>

                </div>

            </form>
        </td>
        <?php if($hooks_html){ ?>
            <td class="center_cell" valign="top">
                <div><?php echo LANG_OR; ?></div>
            </td>
            <td class="right_cell" valign="top">
                <h3><?php echo LANG_LOG_IN_OPENID; ?></h3>
                <?php echo html_each($hooks_html); ?>
            </td>
        <?php } ?>
    </tr>
</table>

<?php if($is_ajax){ ?>
    </div>
<?php } ?>