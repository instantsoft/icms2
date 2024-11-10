<?php if (!$request->isAjax()) { ?>
    <h1><?php echo LANG_GROUPS_DELETE; ?></h1>
<?php } ?>

<h3 class="mb-4"><?php printf(LANG_GROUPS_DELETE_CONFIRM, $group['title']); ?></h3>

<form action="<?php echo $this->href_to($group['slug'], 'delete'); ?>" method="post">
    <?php if (!$group['is_approved']){ ?>
        <div class="form-group">
            <label><?php echo LANG_MODERATION_REFUSE_REASON; ?></label>
            <?php echo html_textarea('reason', '', ['rows' => 10]); ?>
        </div>
    <?php } ?>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="is_delete_content" checked value="1" class="custom-control-input" id="is_delete_content">
            <label class="custom-control-label" for="is_delete_content"><?php echo LANG_GROUPS_DELETE_CONTENT; ?></label>
        </div>
    </div>
    <?php echo html_csrf_token(); ?>
    <div class="buttons mt-4">
        <?php echo html_submit(LANG_CONFIRM); ?>
        <?php if ($request->isAjax()) { ?>
            <?php echo html_button(LANG_CANCEL, 'cancel', '', ['data-dismiss' => 'modal']); ?>
        <?php } ?>
    </div>
</form>