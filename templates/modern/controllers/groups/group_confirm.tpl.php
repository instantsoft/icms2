<h5 class="mb-3"><?php echo $confirm['title']; ?></h5>

<form action="<?php echo $confirm['action']; ?>" method="post">
    <?php echo html_csrf_token(); ?>
    <?php echo html_submit(LANG_CONFIRM); ?>
    <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.modal.close()'); ?>
</form>