<div id="groups_invite_window">

    <h3><?php echo LANG_GROUPS_INVITE; ?></h3>

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

        <p><?php echo LANG_GROUPS_INVITE_NO_FRIENDS;?></p>

    <?php } ?>

</div>
