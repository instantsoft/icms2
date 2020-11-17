<div class="item" id="staff-<?php echo $member['id']; ?>">
    <div class="icon">
        <?php echo html_avatar_image($member['avatar'], 'micro'); ?>
    </div>
    <div class="title">
        <a href="<?php echo href_to_profile($member); ?>"><?php html($member['nickname']); ?></a>
    </div>
    <div class="actions">
        <?php if ($member['id'] != $group['owner_id']) { ?>
            <a class="ajaxlink" href="javascript:" onclick="icms.groups.deleteStaff(<?php echo $member['id']; ?>)"><?php echo LANG_CANCEL; ?></a>
            <div class="loading-icon" style="display:none"></div>
        <?php } ?>
    </div>
</div>
