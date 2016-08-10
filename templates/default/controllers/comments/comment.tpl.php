<?php // Шаблон одного комментария // ?>

<?php
	$is_guests_allowed = !empty($this->controller->options['is_guests']);
    $is_can_add = ($user->is_logged && cmsUser::isAllowed('comments', 'add')) || (!$user->is_logged && $is_guests_allowed);
    $is_highlight_new = isset($is_highlight_new) ? $is_highlight_new : false;
    if (!isset($is_can_rate)) { $is_can_rate = false; }
?>

<?php foreach($comments as $entry){ ?>

<?php

    if (!isset($is_levels)){ $is_levels = true; }
    if (!isset($is_controls)){ $is_controls = true; }
    if (!isset($is_show_target)){ $is_show_target = false; }

    if ($is_show_target){
        $target_url = href_to($entry['target_url']) . "#comment_{$entry['id']}";
    }

    if ($is_controls){
        $is_can_edit = cmsUser::isAllowed('comments', 'edit', 'all') || (cmsUser::isAllowed('comments', 'edit', 'own') && $entry['user']['id'] == $user->id);
        $is_can_delete = cmsUser::isAllowed('comments', 'delete', 'all') || (cmsUser::isAllowed('comments', 'delete', 'own') && $entry['user']['id'] == $user->id);
    }

    $is_selected = $is_highlight_new && ((int)strtotime($entry['date_pub']) > (int)strtotime($user->date_log));

?>

<div id="comment_<?php echo $entry['id']; ?>" class="comment<?php if($is_selected){ ?> selected-comment<?php } ?><?php if($target_user_id == $entry['user_id']){ ?> is_topic_starter<?php } ?>" <?php if ($is_levels) { ?>style="margin-left: <?php echo ($entry['level']-1)*30; ?>px" data-level="<?php echo $entry['level']; ?>"<?php } ?>>
    <?php if($entry['is_deleted']){ ?>
        <span class="deleted"><?php echo LANG_COMMENT_DELETED; ?></span>
        <span class="nav">
            <?php if ($entry['parent_id']){ ?>
                <a href="#up" class="scroll-up" onclick="return icms.comments.up(<?php echo $entry['parent_id']; ?>, <?php echo $entry['id']; ?>)" title="<?php html( LANG_COMMENT_SHOW_PARENT ); ?>">&uarr;</a>
            <?php } ?>
            <a href="#down" class="scroll-down" onclick="return icms.comments.down(this)" title="<?php echo html( LANG_COMMENT_SHOW_CHILD ); ?>">&darr;</a>
        </span>
    <?php } ?>
    <?php if(!$entry['is_deleted']){ ?>
    <div class="info">
        <div class="name">
			<?php if ($entry['user_id']) { ?>
				<a class="user" href="<?php echo href_to('users', $entry['user']['id']); ?>"><?php echo $entry['user']['nickname']; ?></a>
			<?php } else { ?>
				<span class="guest_name">
					<?php echo $entry['author_name']; ?>
				</span>
				<?php if ($user->is_admin && !empty($entry['author_url'])) { ?>
					<span class="guest_ip">
						[<?php echo $entry['author_url']; ?>]
					</span>
				<?php } ?>
			<?php } ?>
            <?php if($is_show_target){ ?>
                &rarr;
                <a class="subject" href="<?php echo $target_url; ?>"><?php html($entry['target_title']); ?></a>
            <?php } ?>
        </div>
        <div class="date">
            <?php echo html_date_time($entry['date_pub']); ?>
        </div>
        <?php if ($is_controls){ ?>
            <div class="nav">
                <a href="#comment_<?php echo $entry['id']; ?>" name="comment_<?php echo $entry['id']; ?>" title="<?php html( LANG_COMMENT_ANCHOR ); ?>">#</a>
                <?php if ($entry['parent_id']){ ?>
                    <a href="#up" class="scroll-up" onclick="return icms.comments.up(<?php echo $entry['parent_id']; ?>, <?php echo $entry['id']; ?>)" title="<?php html( LANG_COMMENT_SHOW_PARENT ); ?>">&uarr;</a>
                <?php } ?>
                <a href="#down" class="scroll-down" onclick="return icms.comments.down(this)" title="<?php echo html( LANG_COMMENT_SHOW_CHILD ); ?>">&darr;</a>
            </div>
            <div class="rating">
                <span class="value <?php echo html_signed_class($entry['rating']); ?>"><?php echo $entry['rating'] ? html_signed_num($entry['rating']) : ''; ?></span>
                <?php if ($is_can_rate && ($entry['user_id'] != $user->id) && !$entry['is_rated']){ ?>
                    <div class="buttons">
                        <a href="#rate-up" class="rate-up" title="<?php echo html( LANG_COMMENT_RATE_UP ); ?>" data-id="<?php echo $entry['id']; ?>"></a>
                        <a href="#rate-down" class="rate-down" title="<?php echo html( LANG_COMMENT_RATE_DOWN ); ?>" data-id="<?php echo $entry['id']; ?>"></a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div class="body">
        <div <?php if (!empty($entry['user']['is_online'])){ ?>class="avatar comment_user_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="avatar"<?php } ?>>
            <a href="<?php echo href_to('users', $entry['user']['id']); ?>">
                <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
            </a>
        </div>
        <div class="content">
            <div class="text">
                <?php echo $entry['content_html']; ?>
            </div>
            <?php if ($is_controls){ ?>
                <div class="links">
                    <?php if ($is_can_add){ ?>
                        <a href="#reply" class="reply" onclick="return icms.comments.add(<?php echo $entry['id']; ?>)"><?php echo LANG_REPLY; ?></a>
                    <?php } ?>
                    <?php if ($is_can_edit){ ?>
                        <a href="#edit" class="edit" onclick="return icms.comments.edit(<?php echo $entry['id']; ?>)"><?php echo LANG_EDIT; ?></a>
                    <?php } ?>
                    <?php if ($is_can_delete){ ?>
                        <a href="#delete" class="delete" onclick="return icms.comments.remove(<?php echo $entry['id']; ?>)"><?php echo LANG_DELETE; ?></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>

<?php } ?>