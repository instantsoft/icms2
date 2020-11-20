<div class="icms-forms__full-msg">
    <div class="success-text">
        <?php echo $success_text; ?>
    </div>
    <?php if (!empty($form_data['options']['continue_link'])) { ?>
        <a href="<?php echo $form_data['options']['continue_link']; ?>">
            <?php echo LANG_CONTINUE; ?>
        </a>
    <?php } ?>
    <?php if(!$hide_after_submit){ ?>
        <a href="#" class="close" onclick="return icms.cforms.closeSuccess(this);">
            <span>&times;</span>
        </a>
    <?php } ?>
</div>