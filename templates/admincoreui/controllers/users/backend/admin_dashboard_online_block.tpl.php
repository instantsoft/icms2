<ul class="list-unstyled need-scrollbar" id="icms_users_wrap">
    <?php foreach ($profiles as $item) { ?>
    <li class="media my-3">
        <div class="media-body">
            <div class="avatar float-left mr-2">
                <img class="img-avatar" src="<?php echo html_avatar_image_src($item['avatar'], 'micro'); ?>" alt="<?php echo html($item['nickname']); ?>">
                <span class="avatar-status badge-success"></span>
            </div>
            <h5 class="mt-0 mb-1">
                <a href="<?php echo href_to_profile($item); ?>">
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