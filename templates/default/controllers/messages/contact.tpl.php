<?php // Шаблон панели диалога // ?>

<?php if ($contact){ ?>
<div id="pm_contact">

    <div class="overview">
        <a href="<?php echo href_to('users', $contact['id']); ?>">
            <span><?php echo html_avatar_image($contact['avatar'], 'micro'); ?></span>
            <span><?php echo $contact['nickname']; ?></span>
        </a>
        <div class="actions">
            <?php if (!$contact['is_admin'] && !$contact['is_ignored']){ ?>
                <?php echo html_button(LANG_PM_ACTION_IGNORE, 'ignore', 'icms.messages.ignoreContact('.$contact['id'].')', array('class'=>'button-small')); ?>
            <?php } ?>
            <?php echo html_button(LANG_DELETE, 'delete', 'icms.messages.deleteContact('.$contact['id'].')', array('class'=>'button-small')); ?>
        </div>
    </div>

    <div id="pm_chat" class="chat">

        <?php if($has_older){ ?>
            <div class="older-loading"></div>
            <a class="show-older" href="#show-older" onclick="icms.messages.showOlder(<?php echo $contact['id'] ?>, this)" rel="<?php echo $messages[0]['id']; ?>"><?php echo LANG_PM_SHOW_OLDER_MESSAGES; ?></a>
        <?php } ?>

        <?php if ($messages){ ?>

            <?php echo $this->renderChild('message', array('messages'=>$messages, 'user'=>$user)); ?>

        <?php } ?>


    </div>

    <div class="composer">

        <?php if ($contact['is_ignored']){ ?>

            <span class="ignored_info">
                <?php echo LANG_PM_CONTACT_IS_IGNORED; ?> 
                <?php echo html_button(LANG_PM_ACTION_FORGIVE, 'forgive', 'icms.messages.forgiveContact('.$contact['id'].')', array('class'=>'button-small')); ?>
            </span>

        <?php } else if ($is_me_ignored){ ?>

            <span class="ignored_info"><?php echo LANG_PM_YOU_ARE_IGNORED; ?></span>

        <?php } else if ($is_private){ ?>

            <span class="ignored_info"><?php echo LANG_PM_CONTACT_IS_PRIVATE; ?></span>

        <?php } else { ?>

            <form action="<?php echo $this->href_to('send'); ?>" method="post">
                <?php echo html_input('hidden', "contact_id", $contact['id']); ?>
                <?php echo html_csrf_token(); ?>
                <div class="editor">
                    <?php echo html_editor('content'); ?>
                </div>
                <div class="buttons">
                    <?php echo html_button(LANG_SEND, 'send', 'icms.messages.send()'); ?>
                </div>
            </form>

        <?php } ?>

    </div>

</div>
<?php } ?>
