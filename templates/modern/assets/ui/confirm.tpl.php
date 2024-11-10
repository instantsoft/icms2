<div class="modal_padding">
    <h3 class="mb-4"><?php echo $confirm_title; ?></h3>
    <form action="<?php html($confirm_action); ?>" method="post">
        <?php echo html_csrf_token(); ?>
        <?php echo html_input('hidden', 'back', $this->controller->request->get('back', '')); ?>
        <?php echo html_submit(LANG_CONFIRM); ?>
        <?php echo html_button(LANG_CANCEL, 'cancel', '', ['data-dismiss' => 'modal']); ?>
    </form>
</div>