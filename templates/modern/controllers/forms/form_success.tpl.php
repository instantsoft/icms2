<div class="d-flex flex-column alert alert-dismissible align-items-center justify-content-center w-100 h-100 position-absolute icms-forms__full-msg">
    <div class="success-text display-4 text-center">
        <?php echo $success_text; ?>
    </div>
    <?php if (!empty($form_data['options']['continue_link'])) { ?>
        <a href="<?php echo $form_data['options']['continue_link']; ?>" class="mt-3 btn btn-success">
            <?php echo LANG_CONTINUE; ?>
        </a>
    <?php } ?>
    <?php if(!$hide_after_submit){ ?>
        <button type="button" class="close" onclick="return icms.cforms.closeSuccess(this);">
            <span>&times;</span>
        </button>
    <?php } ?>
</div>