<div id="groups_invite_window">

    <?php if ($groups){ ?>

        <?php
            $items = array();
            foreach($groups as $id=>$group){ $items[$id] = $group['title']; }
        ?>

        <form action="<?php echo $this->href_to('invite', $invited_id); ?>" method="post">

            <div class="list">
                <?php echo html_select('group_id', $items); ?>
            </div>

            <div class="buttons">
                <?php echo html_submit(LANG_INVITE); ?>
            </div>

        </form>

    <?php } else {  ?>

        <p><?php echo LANG_GROUPS_INVITE_NO_GROUPS;?></p>

    <?php } ?>

</div>
