<?php // Шаблон одной записи на стене // ?>

<?php
    $count = 0;
    if (empty($max_entries)){ $max_entries = false; }
    if (empty($page)){ $page = false; }
?>

<?php foreach($entries as $entry){ ?>

<?php

    $count++;

    $is_can_add = !empty($permissions['reply']) && $entry['parent_id'] == 0;
    $is_can_edit = ($entry['user']['id']==$user->id) || $user->is_admin;
    $is_can_delete = ($entry['user']['id']==$user->id) || $permissions['delete'];

    if (empty($entry['replies_count'])){ $entry['replies_count'] = 0; }

    $is_hidden = ($max_entries && ($count > $max_entries) && ($page == 1));

?>

<div id="entry_<?php echo $entry['id']; ?>" class="entry"<?php if($is_hidden){ ?> style="display:none"<?php } ?> data-replies="<?php echo $entry['replies_count']; ?>">
    <div class="body">
        <div class="avatar">
            <a href="<?php echo href_to_profile($entry['user']); ?>" <?php if (!empty($entry['user']['is_online'])){ ?>class="peer_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="peer_no_online"<?php } ?>>
                <?php echo html_avatar_image($entry['user']['avatar'], ($entry['parent_id'] ? 'micro' : 'micro'), $entry['user']['nickname']); ?>
            </a>
        </div>
        <div class="content">
            <div class="info">
                <div class="name">
                    <a class="user" href="<?php echo href_to_profile($entry['user']); ?>"><?php echo $entry['user']['nickname']; ?></a>
                </div>
                <div class="date<?php if(!empty($entry['is_new'])){ ?> highlight_new<?php } ?>">
                    <?php echo html(string_date_age_max($entry['date_pub'], true)); ?>
                    <?php if ($entry['date_last_modified']){ ?>
                        <span class="date_last_modified" title="<?php echo strip_tags(html_date_time($entry['date_last_modified'])); ?>">(<?php echo mb_strtolower(LANG_CONTENT_EDITED); ?>)</span>
                    <?php } ?>
                </div>
                <div class="anchor">
                    <a href="?wid=<?php echo $entry['id']; ?>" title="<?php echo LANG_WALL_ENTRY_ANCHOR; ?>">#</a>
                </div>
            </div>
            <div class="text">
                <?php echo $entry['content_html']; ?>
            </div>
        </div>
    </div>
    <div class="links<?php if ($entry['replies_count']){ ?> has_replies<?php } ?>">
        <?php if ($entry['replies_count']){ ?>
        <a href="#wall-replies" class="get_replies" onclick="return icms.wall.replies(<?php echo $entry['id']; ?>)"><?php echo html_spellcount($entry['replies_count'], LANG_REPLY_SPELLCOUNT); ?></a>
        <?php } ?>
        <?php if ($is_can_add){ ?>
            <a href="#wall-reply" class="reply" onclick="return icms.wall.add(<?php echo $entry['id']; ?>)"><?php echo LANG_REPLY; ?></a>
        <?php } ?>
        <?php if ($is_can_edit){ ?>
            <a href="#wall-edit" class="edit" onclick="return icms.wall.edit(<?php echo $entry['id']; ?>)"><?php echo LANG_EDIT; ?></a>
        <?php } ?>
        <?php if ($is_can_delete){ ?>
            <a href="#wall-delete" class="delete" onclick="return icms.wall.remove(<?php echo $entry['id']; ?>)"><?php echo LANG_DELETE; ?></a>
        <?php } ?>
    </div>
    <?php if (!$entry['parent_id']) { ?>
        <div class="replies_loading">
            <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
        </div>
        <div class="replies"></div>
    <?php } ?>
</div>

<?php if ($max_entries && ($count == $max_entries) && (count($entries) > $count) && ($page == 1)){ ?>
    <div class="show_more">
        <a href="#wall-more" onclick="return icms.wall.more()"><?php echo LANG_SHOW_ALL; ?></a>
    </div>
<?php } ?>

<?php } ?>