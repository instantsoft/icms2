<div class="item media mb-3 align-items-center" id="staff-<?php echo $member['id']; ?>">
    <div class="mr-3 icms-user-avatar small">
        <?php if($member['avatar']){ ?>
            <?php echo html_avatar_image($member['avatar'], 'micro', $member['nickname']); ?>
        <?php } else { ?>
            <?php echo html_avatar_image_empty($member['nickname'], 'avatar__mini'); ?>
        <?php } ?>
    </div>
    <div class="media-body text-truncate">
        <a href="<?php echo href_to_profile($member); ?>">
            <?php html($member['nickname']); ?>
        </a>
    </div>
    <div class="actions">
        <?php if ($member['id'] != $group['owner_id']) { ?>
            <a class="ajaxlink btn btn-danger btn-sm icms-group-sraff__delete" href="#" onclick="icms.groups.deleteStaff(<?php echo $member['id']; ?>)">
                <?php html_svg_icon('solid', 'window-close'); ?>
                <span class="d-none d-md-inline-block"><?php echo LANG_CANCEL; ?></span>
            </a>
        <?php } ?>
    </div>
</div>