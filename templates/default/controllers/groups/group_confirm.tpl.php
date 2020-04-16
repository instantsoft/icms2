<div class="modal_padding">

    <h3><?php echo $confirm['title']; ?></h3>

    <form action="<?php echo $confirm['action']; ?>" method="post">
        <?php echo html_csrf_token(); ?>
        <?php echo html_submit(LANG_CONFIRM); ?>
        <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.modal.close()'); ?>
    </form>

</div>