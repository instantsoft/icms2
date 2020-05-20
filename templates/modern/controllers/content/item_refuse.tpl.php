<div class="modal_padding width_480">
    <form action="<?php echo href_to($ctype['name'], 'delete', $item['id']); ?>" method="post">
        <?php echo html_csrf_token(); ?>
        <fieldset>
            <div class="field ft_text">
                <label><?php echo LANG_MODERATION_REFUSE_REASON; ?></label>
                <?php echo html_textarea('reason', '', array('rows'=>10)); ?>
            </div>
        </fieldset>
        <div class="buttons mt-3">
            <?php echo html_submit(LANG_CONFIRM); ?>
            <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.modal.close()'); ?>
        </div>
    </form>
</div>