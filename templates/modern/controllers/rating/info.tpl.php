<div id="rating_info_window">

    <?php if (!$votes){ ?>
        <p class="alert alert-info m-0"><?php echo LANG_RATING_NO_VOTES; ?></p>
    <?php } ?>

    <?php if ($votes){ ?>

        <div class="rating_info_list">

            <?php $this->renderChild('info_list', array('votes'=>$votes, 'user' => $user)); ?>

        </div>

        <?php if ($pages > 1){ ?>
            <div class="rating_info_pagination mt-3"
                data-target-controller="<?php echo $target_controller; ?>"
                data-target-subject="<?php echo $target_subject; ?>"
                data-target-id="<?php echo $target_id; ?>"
                data-url="<?php echo $this->href_to('info'); ?>"
                >
                <?php for($p=1; $p<=$pages; $p++){ ?>
                    <a href="#<?php echo $p; ?>" data-page="<?php echo $p; ?>" class="btn btn-primary btn-sm<?php if ($p==$page) { ?> active<?php } ?>">
                        <?php echo $p; ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

    <?php } ?>

</div>