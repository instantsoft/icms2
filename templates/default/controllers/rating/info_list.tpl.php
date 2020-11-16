<?php foreach($votes as $vote){ ?>

    <div class="item">
        <?php if(!empty($vote['user']['id'])){ ?>
            <a href="<?php echo href_to_profile($vote['user']); ?>"><?php html($vote['user']['nickname']); ?></a>
        <?php } else { ?>
            <span><?php html($vote['user']['nickname']); ?></span>
        <?php } ?>
        <?php if($user->is_admin){ ?>
            <span> [<?php html($vote['ip']); ?>]</span>
        <?php } ?>
        <span class="score <?php echo html_signed_class($vote['score']); ?>"><?php echo html_signed_num($vote['score']); ?></span>
    </div>

<?php }
