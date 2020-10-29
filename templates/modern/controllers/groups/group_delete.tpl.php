<?php $cancel_act = $request->isAjax() ? 'icms.modal.close()' : 'window.history.go(-1)'; ?>

<?php if (!$request->isAjax()) { ?>
    <h1><?php echo LANG_GROUPS_DELETE; ?></h1>
<?php } ?>

<?php if ($request->isAjax()) { ?><div class="modal_padding width_480"><?php } ?>
<h3><?php printf(LANG_GROUPS_DELETE_CONFIRM, $group['title']); ?></h3>

<form action="<?php echo $this->href_to($group['slug'], 'delete'); ?>" method="post">
    <?php if (!$group['is_approved']){ ?>
        <div class="form-group">
            <label><?php echo LANG_MODERATION_REFUSE_REASON; ?></label>
            <?php echo html_textarea('reason', '', array('rows'=>10)); ?>
        </div>
    <?php } ?>
    <div class="form-check">
        <?php echo html_checkbox('is_delete_content', true, 1, ['id' => 'is_delete_content']); ?>
        <label class="form-check-label" for="is_delete_content">
            <?php echo LANG_GROUPS_DELETE_CONTENT; ?>
        </label>
    </div>
    <?php echo html_csrf_token(); ?>
    <div class="buttons mt-3">
        <?php echo html_submit(LANG_CONFIRM); ?>
        <?php echo html_button(LANG_CANCEL, 'cancel', $cancel_act); ?>
    </div>
</form>
<?php if ($request->isAjax()) { ?></div><?php } ?>