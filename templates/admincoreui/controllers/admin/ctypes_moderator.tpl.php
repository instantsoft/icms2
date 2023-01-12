<tr id="moderator-<?php echo $moderator['user_id']; ?>">
    <td>
        <?php echo html_avatar_image($moderator['user_avatar'], 'micro'); ?>
        <a class="ml-2" href="<?php echo href_to_profile($moderator['user']); ?>">
            <?php html($moderator['user_nickname']); ?>
        </a>
    </td>
    <td class="d-none d-lg-table-cell"><?php echo html_date($moderator['date_assigned']);?></td>
    <?php if(empty($not_use_trash)){ ?>
        <td>
            <a title="<?php echo LANG_MODERATOR_TRASH_LEFT_TIME; ?>" class="ajax-modal ajaxlink btn btn-warning trash_left_time_num" href="<?php echo href_to('admin', 'controllers', ['edit', 'moderation', 'edit_trash_left_time', $moderator['id']]); ?>">
            <?php if($moderator['trash_left_time'] !== null){ ?>
                <?php if($moderator['trash_left_time']){ ?>
                    <?php echo html_spellcount($moderator['trash_left_time'], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);?>
                <?php } else { ?>
                    <?php echo LANG_MODERATION_TRASH_NO_REMOVE; ?>
                <?php } ?>
            <?php } else { ?>
                <?php echo LANG_BY_DEFAULT; ?>
            <?php } ?>
            </a>
        </td>
    <?php } ?>
    <td class="d-none d-lg-table-cell"><?php echo $moderator['count_approved'];?></td>
    <td class="d-none d-lg-table-cell"><?php echo $moderator['count_deleted'];?></td>
    <td><?php echo $moderator['count_idle'];?></td>
    <td>
        <div class="actions">
            <a class="view" href="<?php echo href_to('admin', 'controllers', ['edit', 'moderation', 'logs', (!isset($ctype['controller']) ? 'content' : $ctype['controller']), $ctype['name'], 0, $moderator['user_id']]); ?>" title="<?php echo LANG_VIEW; ?>"></a>
            <a class="delete" href="javascript:" onclick="icms.adminModerators.cancel(<?php echo $moderator['user_id']; ?>)" title="<?php echo LANG_CANCEL; ?>"></a>
            <div class="loading-icon" style="display:none"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>
        </div>
    </td>
</tr>