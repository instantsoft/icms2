<div class="modal_padding">
    <h3><?php echo $confirm_title; ?></h3>
    <form action="<?php html($confirm_action); ?>" method="post" onsubmit="$(this).find('.button-submit').addClass('disabled is-busy');">
        <?php echo html_csrf_token(); ?>
        <?php echo html_input('hidden', 'back', $this->controller->request->get('back', '')); ?>
        <?php echo html_submit(LANG_CONFIRM); ?>
        <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.modal.close()'); ?>
    </form>
</div>