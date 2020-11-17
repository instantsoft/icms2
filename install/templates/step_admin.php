<h1><?php echo LANG_STEP_ADMIN; ?></h1>

<?php if ($is_external_users) { ?>

    <p><?php echo sprintf(LANG_ADMIN_EXTERNAL, $users_table); ?></p>

<?php } ?>

<?php if (!$is_external_users) { ?>

    <p><?php echo LANG_ADMIN_INFO; ?></p>

    <form id="step-form">

        <fieldset>

            <div class="field">
                <label><?php echo LANG_ADMIN_NAME; ?></label>
                <input type="text" class="input input-icon icon-user" autocomplete="off" name="nickname" value="" />
            </div>

            <div class="field">
                <label><?php echo LANG_ADMIN_EMAIL; ?></label>
                <input type="text" class="input input-icon icon-email" autocomplete="off" name="email" value="" />
            </div>

            <div class="field">
                <label><?php echo LANG_ADMIN_PASS; ?></label>
                <input type="password" class="input input-icon icon-password" autocomplete="off" name="pass1" value="" />
            </div>

            <div class="field">
                <label><?php echo LANG_ADMIN_PASS2; ?></label>
                <input type="password" class="input input-icon icon-password2" autocomplete="off" name="pass2" value="" />
            </div>

        </fieldset>

    </form>

<?php } ?>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>