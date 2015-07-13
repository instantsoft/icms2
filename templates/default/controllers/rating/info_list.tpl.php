<?php foreach($votes as $vote){ ?>

    <div class="item">
        <a href="<?php echo href_to('users', $vote['user']['id']); ?>"><?php html($vote['user']['nickname']); ?></a>
        <span class="score <?php echo html_signed_class($vote['score']); ?>"><?php echo html_signed_num($vote['score']); ?></span>
    </div>

<?php } ?>
