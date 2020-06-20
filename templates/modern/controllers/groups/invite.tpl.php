<div id="groups_invite_window">
    <?php if ($groups){ ?>

        <?php
            $items = [];
            foreach($groups as $id=>$group){ $items[$id] = $group['title']; }
        ?>

        <form action="<?php echo $this->href_to('invite', $invited_id); ?>" method="post" class="form-inline">
            <?php echo html_select('group_id', $items, '', ['class' => 'mr-3']); ?>
            <?php echo html_submit(LANG_INVITE); ?>
        </form>

    <?php } else {  ?>
        <p class="alert alert-warning mt-3 mb-0" role="alert"><?php echo LANG_GROUPS_INVITE_NO_GROUPS;?></p>
    <?php } ?>
</div>