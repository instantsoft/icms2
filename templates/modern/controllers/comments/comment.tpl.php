<?php
	$limit_nesting = !empty($this->controller->options['limit_nesting']) ? $this->controller->options['limit_nesting'] : 0;
	$dim_negative = !empty($this->controller->options['dim_negative']);
    $is_highlight_new = isset($is_highlight_new) ? $is_highlight_new : false;
    if (!isset($is_can_rate)) { $is_can_rate = false; }
?>

<?php foreach($comments as $entry){

    $no_approved_class = $entry['is_approved'] ? '' : 'no_approved';

    $author_url = href_to_profile($entry['user']);

    if ($is_show_target){
        $target_url = rel_to_href($entry['target_url']) . "#comment_{$entry['id']}";
    }

    $is_selected = $is_highlight_new && (strtotime($entry['date_pub']) > strtotime($user->date_log));

    $level = 0;
    if($is_levels){
        $level = (($limit_nesting && $entry['level'] > $limit_nesting) ? $limit_nesting : ($entry['level']-1));
    }

?>

<div id="comment_<?php echo $entry['id']; ?>" class="media my-3 my-lg-4 comment<?php if($is_selected){ ?> selected-comment shadow<?php } ?> icms-comments-ns ns-<?php echo $level; ?>" data-level="<?php echo $entry['level']; ?>">
    <?php if(!$entry['is_deleted']){ ?>
        <div class="d-flex align-items-start flex-column mr-2 mr-lg-3 icms-comment-rating <?php echo $no_approved_class; ?>">
            <div class="d-flex align-items-center flex-column w-100">
                <?php if ($is_can_rate && ($entry['user_id'] != $user->id) && empty($entry['is_rated'])){ ?>
                    <a href="#rate-up" class="icms-comment-rating_btn text-success rate-up" title="<?php echo html( LANG_COMMENT_RATE_UP ); ?>" data-id="<?php echo $entry['id']; ?>">
                        <?php html_svg_icon('solid', 'caret-square-up'); ?>
                    </a>
                <?php } else { ?>
                    <span class="rate-disabled">
                        <?php html_svg_icon('solid', 'caret-square-up'); ?>
                    </span>
                <?php } ?>
                <span class="value <?php echo html_signed_class($entry['rating']); ?>">
                    <?php echo $entry['rating'] ? html_signed_num($entry['rating']) : '0'; ?>
                </span>
                <?php if ($is_can_rate && ($entry['user_id'] != $user->id) && empty($entry['is_rated'])){ ?>
                    <a href="#rate-down" class="icms-comment-rating_btn rate-down text-danger" title="<?php echo html( LANG_COMMENT_RATE_DOWN ); ?>" data-id="<?php echo $entry['id']; ?>">
                        <?php html_svg_icon('solid', 'caret-square-down'); ?>
                    </a>
                <?php } else { ?>
                    <span class="rate-disabled">
                        <?php html_svg_icon('solid', 'caret-square-down'); ?>
                    </span>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="media-body">

        <h6 class="d-md-flex align-items-center mb-3">
            <span class="d-none d-sm-inline-block mr-2">
                <?php if ($entry['user_id']) { ?>
                    <a href="<?php echo $author_url; ?>" class="icms-user-avatar <?php if (!empty($entry['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                        <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                    </a>
                <?php } else { ?>
                    <span class="icms-user-avatar">
                        <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                    </span>
                <?php } ?>
            </span>
            <?php if ($entry['user_id']) { ?>
                <a href="<?php echo $author_url; ?>" class="user <?php if($entry['user_id'] && $target_user_id == $entry['user_id']){ ?>btn btn-success btn-sm border-0<?php } ?>"><?php echo $entry['user']['nickname']; ?></a>
            <?php } else { ?>
                <span class="guest_name user"><?php echo $entry['author_name']; ?></span>
                <?php if ($user->is_admin && !empty($entry['author_ip'])) { ?>
                    <span class="guest_ip">
                        [<?php echo $entry['author_ip']; ?>]
                    </span>
                <?php } ?>
            <?php } ?>
            <?php if($is_show_target){ ?>
                <span class="mx-md-2">&rarr;</span>
                <a class="subject" href="<?php echo $target_url; ?>">
                    <?php html($entry['target_title']); ?>
                </a>
            <?php } ?>
            <?php if (empty($entry['hide_date'])) { ?>
                <small class="text-muted ml-2">
                    <?php html_svg_icon('solid', 'history'); ?>
                    <span class="<?php echo $no_approved_class; ?>">
                        <?php echo string_date_age_max($entry['date_pub'], true); ?>
                    </span>
                    <?php if ($entry['date_last_modified']){ ?>
                        <span data-toggle="tooltip" data-placement="top" class="date_last_modified ml-2" title="<?php echo LANG_CONTENT_EDITED.' '.strip_tags(html_date_time($entry['date_last_modified'])); ?>">
                            <?php html_svg_icon('solid', 'pen'); ?>
                        </span>
                    <?php } ?>
                    <?php if ($no_approved_class){ ?>
                        <span class="hide_approved ml-2">
                            <?php echo html_bool_span(LANG_CONTENT_NOT_APPROVED, false); ?>
                        </span>
                    <?php } ?>
                </small>
            <?php } ?>
            <?php if ($is_controls){ ?>
                <a data-toggle="tooltip" data-placement="top" href="#comment_<?php echo $entry['id']; ?>" class="text-dark ml-2 mr-4" title="<?php html( LANG_COMMENT_ANCHOR ); ?>">#</a>
            <?php } ?>
        </h6>

        <?php if($entry['is_deleted']){ ?>
            <div class="alert alert-secondary">
                <?php echo LANG_COMMENT_DELETED; ?>
            </div>
        <?php } elseif(!empty($entry['hide_controls'])) { ?>
            <?php echo $entry['content_html']; ?>
        <?php } else { ?>
        <div class="icms-comment-html text-break<?php if($dim_negative && $entry['rating'] < 0){ ?> bad<?php echo ($entry['rating'] < -6 ? 6 : abs(intval($entry['rating']))) ?> bad<?php } ?>">
                <?php echo $entry['content_html']; ?>
            </div>
        <?php } ?>

        <?php if (!$entry['is_deleted'] && empty($entry['hide_controls']) && !empty($entry['actions'])){ ?>
            <div class="icms-comment-controls mt-1">
                <?php foreach($entry['actions'] as $action){ ?>
                    <a href="<?php echo $action['href']; ?>" class="btn btn-sm border-0<?php if (!empty($action['class'])){ ?> <?php echo $action['class']; ?><?php } ?>"<?php if (!empty($action['onclick'])) { ?> onclick="<?php echo $action['onclick']; ?>"<?php } ?><?php if (!empty($action['hint'])) { ?> title="<?php html($action['hint']); ?>"<?php } ?>>
                        <?php if (!empty($action['icon'])){ ?>
                            <?php html_svg_icon('solid', $action['icon']); ?>
                        <?php } ?>
                        <?php if (!empty($action['title'])){ ?>
                            <?php echo $action['title']; ?>
                        <?php } ?>
                    </a>
                <?php } ?>
                <?php if ($is_controls){ ?>
                    <?php if ($entry['parent_id']){ ?>
                        <a href="#up" class="btn btn-sm border-0 scroll-up ml-2" onclick="return icms.comments.up(<?php echo $entry['parent_id']; ?>, <?php echo $entry['id']; ?>)" title="<?php html( LANG_COMMENT_SHOW_PARENT ); ?>">&uarr;</a>
                    <?php } ?>
                    <a href="#down" class="btn btn-sm border-0 d-none scroll-down" onclick="return icms.comments.down(this)" title="<?php echo html( LANG_COMMENT_SHOW_CHILD ); ?>">&darr;</a>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

</div>

<?php } ?>
