<?php if ($items){ ?>
<ul class="list-unstyled need-scrollbar m-0" id="activity_list">
    <?php foreach ($items as $item) { ?>
    <li class="media my-3">
        <div class="media-body">
            <div class="avatar float-left mr-2">
                <img class="img-avatar" src="<?php echo html_avatar_image_src($item['user']['avatar'], 'micro'); ?>" alt="<?php echo html($item['user']['nickname']); ?>">
                <span class="avatar-status badge-<?php if (!empty($item['user']['is_online'])){ ?>success<?php } else { ?>danger<?php } ?>"></span>
            </div>
            <h5 class="mt-0 mb-1">
                <a href="<?php echo href_to('users', $item['user']['id']); ?>">
                    <?php html($item['user']['nickname']); ?>
                </a>
            </h5>
            <span class="text-muted <?php if(!empty($item['is_new'])){ ?> text-warning<?php } ?>">
                <?php echo $item['date_diff']; ?>
            </span>
            <?php echo $item['description']; ?>
        </div>
        <?php if (!empty($item['images'][0]['src'])) { ?>
            <img width="64" class="mr-3" src="<?php echo $item['images'][0]['src']; ?>" alt="">
        <?php } ?>
    </li>
    <?php } ?>
</ul>
<?php } else { echo LANG_LIST_EMPTY; } ?>