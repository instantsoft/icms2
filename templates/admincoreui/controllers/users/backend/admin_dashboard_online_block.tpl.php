<ul class="list-unstyled need-scrollbar" id="icms_users_wrap">
    <?php foreach ($profiles as $item) { ?>
    <?php $url = href_to_profile($item); ?>
    <li class="media my-3">
        <a href="<?php echo $url; ?>" class="icms-user-avatar mr-2 mr-md-3 small peer_online">
            <?php if($item['avatar']){ ?>
                <?php echo html_avatar_image($item['avatar'], 'micro', $item['nickname']); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($item['nickname'], 'avatar__mini'); ?>
            <?php } ?>
        </a>
        <div class="media-body">
            <h5 class="mt-0 mb-1">
                <a href="<?php echo $url; ?>">
                    <?php echo html_strip($item['nickname'], 50); ?>
                </a>
            </h5>
            <span class="text-muted">
                <?php echo LANG_REGISTRATION; ?> <?php echo string_date_age_max($item['date_reg'], true); ?>
            </span>
        </div>
    </li>
    <?php } ?>
</ul>