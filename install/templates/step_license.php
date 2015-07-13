<h1><?php echo LANG_STEP_LICENSE; ?></h1>

<p><?php echo LANG_LICENSE_NOTE; ?></p>

<textarea class="license-text" id="gpl-en" readonly><?php echo $license_text; ?></textarea>

<form id="step-form">
    <p>
        <label>
            <input type="checkbox" value="1" name="agree">
            <?php echo LANG_LICENSE_AGREE; ?>
        </label>
    </p>
</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>
