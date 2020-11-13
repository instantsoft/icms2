<div id="groups_invite_window">
    <?php if ($friends){ ?>
        <form action="<?php echo $this->href_to('invite_friends', $group_id); ?>" method="post">
            <div class="list">
                <?php
                    $users_list_input = new fieldUsers('friends');
                    echo $users_list_input->getInput($friends);
                ?>
            </div>
            <div class="buttons">
                <?php echo html_submit(LANG_INVITE); ?>
            </div>
        </form>
    <?php } else {  ?>
        <div class="alert alert-warning mt-3 mb-0" role="alert"><?php echo LANG_GROUPS_INVITE_NO_FRIENDS;?></div>
    <?php } ?>
</div>