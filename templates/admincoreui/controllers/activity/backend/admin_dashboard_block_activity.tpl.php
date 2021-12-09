<?php if ($items){ ?>
<ul class="list-unstyled need-scrollbar m-0" id="activity_list">
    <?php foreach ($items as $item) { ?>
    <?php $url = href_to_profile($item['user']); ?>
    <li class="media my-3">
        <a href="<?php echo $url; ?>" class="icms-user-avatar mr-2 mr-md-3 small <?php if (!empty($item['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
            <?php if($item['user']['avatar']){ ?>
                <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($item['user']['nickname'], 'avatar__mini'); ?>
            <?php } ?>
        </a>
        <div class="media-body">
            <h5 class="mt-0 mb-1">
                <a href="<?php echo $url; ?>">
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