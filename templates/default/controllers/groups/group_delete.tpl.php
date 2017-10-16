<?php $cancel_act = $request->isAjax() ? 'icms.modal.close()' : 'window.history.go(-1)'; ?>

<?php if (!$request->isAjax()) { ?>
    <h1><?php echo LANG_GROUPS_DELETE; ?></h1>
<?php } ?>

<?php if ($request->isAjax()) { ?><div class="modal_padding width_480"><?php } ?>
<h3><?php printf(LANG_GROUPS_DELETE_CONFIRM, $group['title']); ?></h3>

<form action="<?php echo $this->href_to($group['slug'], 'delete'); ?>" method="post">
    <?php if (!$group['is_approved']){ ?>
        <fieldset>
            <div class="field ft_text">
                <label><?php echo LANG_MODERATION_REFUSE_REASON; ?></label>
                <?php echo html_textarea('reason', '', array('rows'=>10)); ?>
            </div>
        </fieldset>
    <?php } ?>
    <p>
        <label>
            <?php echo html_checkbox('is_delete_content', true); ?>
            <?php echo LANG_GROUPS_DELETE_CONTENT; ?>
        </label>
    </p>
    <?php echo html_csrf_token(); ?>
    <?php echo html_submit(LANG_CONFIRM); ?>
    <?php echo html_button(LANG_CANCEL, 'cancel', $cancel_act); ?>
</form>
<?php if ($request->isAjax()) { ?></div><?php } ?>