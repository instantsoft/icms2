<?php
    $last_date = '';
    $today_date = date('j F Y');
    $yesterday_date = date('j F Y', time()-3600*24);
    $is_can_delete = cmsUser::isAllowed('activity', 'delete');
?>

<div class="icms-activity__list_wd">
    <?php foreach($items as $item) { ?>

        <?php if ($show_date_groups) { ?>
            <?php $item_date = date('j F Y', strtotime($item['date_pub'])); ?>
            <?php if ($item_date != $last_date){ ?>

                <?php
                    switch($item_date){
                        case $today_date: $date = LANG_TODAY; break;
                        case $yesterday_date: $date = LANG_YESTERDAY; break;
                        default: $date = lang_date($item_date);
                    }
                ?>

                <h4 class="icms-activity__list-day">
                    <?php echo $date; ?>
                </h4>
                <?php $last_date = $item_date; ?>

            <?php } ?>
        <?php } ?>

        <?php $url = href_to_profile($item['user']); ?>

        <div class="icms-activity__list-item media mb-3">
            <?php if ($show_avatars){ ?>
                <a href="<?php echo $url; ?>" class="icms-user-avatar mr-2 mr-md-3 small <?php if (!empty($item['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                    <?php if($item['user']['avatar']){ ?>
                        <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($item['user']['nickname'], 'avatar__mini'); ?>
                    <?php } ?>
                </a>
            <?php } ?>
            <div class="media-body">
                <h6 class="my-0">
                    <a class="author" href="<?php echo $url; ?>">
                        <?php html($item['user']['nickname']); ?>
                    </a>
                    <?php echo $item['description']; ?>
                    <?php if ($item['is_private']) { ?>
                        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>">
                            <?php html_svg_icon('solid', 'lock'); ?>
                        </span>
                    <?php } ?>
                </h6>
                <div class="details">
                    <span class="date small text-muted<?php if(!empty($item['is_new'])){ ?> highlight_new<?php } ?>"><?php echo $item['date_diff']; ?></span>
                    <?php if (!empty($item['reply_url']) && cmsUser::isLogged()) { ?>
                        <a href="<?php echo $item['reply_url']; ?>" class="btn btn-sm">
                            <?php html_svg_icon('solid', 'reply'); ?>
                            <?php echo LANG_REPLY; ?>
                        </a>
                    <?php } ?>
                </div>
                <?php if (!empty($item['images'])) { ?>
                    <div class="d-flex justify-content-start flex-wrap">
                        <?php foreach($item['images'] as $image){ ?>
                            <a href="<?php echo $image['url']; ?>" class="mr-1 mt-1">
                                <img src="<?php echo $image['src']; ?>" class="img-fluid" alt="<?php html(!empty($image['title']) ? $image['title'] : $item['subject_title']); ?>">
                            </a>
                        <?php } ?>
                        <?php if($item['images_count'] > 5){ ?>
                            <a class="bg-secondary text-white text-decoration-none h3 m-0 mt-1 px-4 d-flex align-items-center" href="<?php echo $item['subject_url']; ?>">
                                +<span><?php echo ($item['images_count']-4); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($is_can_delete) { ?>
                <div class="dropdown ml-2">
                    <button class="btn btn-dylan" type="button" data-toggle="dropdown">
                        <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item text-danger" href="<?php echo href_to('activity', 'delete', $item['id'], ['csrf_token' => cmsForm::getCSRFToken()]); ?>">
                            <?php html_svg_icon('solid', 'trash'); ?>
                            <?php html(LANG_DELETE); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>

    <?php } ?>
</div>