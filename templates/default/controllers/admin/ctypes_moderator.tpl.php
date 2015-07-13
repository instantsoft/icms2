<tr id="moderator-<?php echo $moderator['user_id']; ?>">
    <td width="32">
        <?php echo html_avatar_image($moderator['user_avatar'], 'micro'); ?>
    </td>
    <td>
        <a href="<?php echo href_to('users', $moderator['user_id']); ?>"><?php html($moderator['user_nickname']); ?></a>
    </td>
    <td class="center"><?php echo html_date($moderator['date_assigned']);?></td>
    <td class="center"><?php echo $moderator['count_approved'];?></td>
    <td class="center"><?php echo $moderator['count_deleted'];?></td>
    <td class="center"><?php echo $moderator['count_idle'];?></td>
    <td>
        <div class="actions">
            <a class="delete" href="javascript:" onclick="icms.adminModerators.cancel(<?php echo $moderator['user_id']; ?>)" title="<?php echo LANG_CANCEL; ?>"></a>
            <div class="loading-icon" style="display:none"></div>
        </div>
    </td>
</tr>
