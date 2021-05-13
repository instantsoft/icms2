<?php foreach($votes as $vote){ ?>
    <div class="item d-flex align-items-center mb-3">
        <?php if(!empty($vote['user']['id'])){ ?>
            <a href="<?php echo href_to_profile($vote['user']); ?>" class="icms-user-avatar mr-3">
            <?php if($vote['user']['avatar']){ ?>
                <?php echo html_avatar_image($vote['user']['avatar'], 'micro', $vote['user']['nickname']); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($vote['user']['nickname'], 'avatar__mini'); ?>
            <?php } ?>
            </a>
            <a href="<?php echo href_to_profile($vote['user']); ?>">
                <?php html($vote['user']['nickname']); ?>
            </a>
        <?php } else { ?>
            <span><?php html($vote['user']['nickname']); ?></span>
        <?php } ?>
        <?php if($user->is_admin){ ?>
            <span class="ml-3"> [<?php html($vote['ip']); ?>]</span>
        <?php } ?>
        <span class="score <?php echo html_signed_class($vote['score']); ?> h3 m-0 ml-auto">
            <?php echo html_signed_num($vote['score']); ?>
        </span>
    </div>
<?php } ?>