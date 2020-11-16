<?php // Шаблон панели диалога // ?>

<?php if ($contact){ ?>
<div id="pm_contact">

    <div class="overview">
        <div id="contact_toggle"></div>
        <a href="<?php echo href_to_profile($contact); ?>">
            <span class="<?php if ($contact['is_online']) { ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                <?php echo html_avatar_image($contact['avatar'], 'micro'); ?>
            </span>
            <span><?php echo $contact['nickname']; ?></span>
        </a>
        <?php if (!$contact['is_online']) { ?>
            <div title="<?php echo LANG_USERS_PROFILE_LOGDATE; ?>" class="user_date_log">
                <?php echo mb_strtolower(LANG_USERS_PROFILE_LOGDATE); ?> <span><?php echo mb_strtolower(string_date_age_max($contact['date_log'], true)); ?></span>
            </div>
        <?php } ?>
        <div class="actions">
            <?php echo html_button(LANG_DELETE, 'delete_msgs', 'icms.messages.deleteMsgs()', array('class'=>'button-small button_hide', 'id' => 'delete_msgs')); ?>
            <?php if (!$contact['is_admin'] && !$contact['is_ignored']){ ?>
                <?php echo html_button(LANG_PM_ACTION_IGNORE, 'ignore', 'icms.messages.ignoreContact('.$contact['id'].')', array('class'=>'button-small')); ?>
            <?php } ?>
            <?php echo html_button(LANG_PM_DELETE_CONTACT, 'delete', 'icms.messages.deleteContact('.$contact['id'].')', array('class'=>'button-small')); ?>
        </div>
        <div class="toogle-actions"></div>
    </div>

    <div id="pm_chat" class="chat">

        <?php if($has_older){ ?>
            <div class="older-loading"></div>
            <a class="show-older" href="#show-older" onclick="return icms.messages.showOlder(<?php echo $contact['id'] ?>, this);" rel="<?php echo $messages[0]['id']; ?>"><?php echo LANG_PM_SHOW_OLDER_MESSAGES; ?></a>
        <?php } ?>

        <?php if ($messages){ ?>

            <?php echo $this->renderChild('message', array('messages'=>$messages, 'user'=>$user, 'last_date' => '')); ?>

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
                <?php echo html_input('hidden', 'last_date', '', array('id' => 'msg_last_date')); ?>
                <?php echo html_input('hidden', 'contact_id', $contact['id']); ?>
                <?php echo html_csrf_token(); ?>
                <div class="editor editor-<?php echo $editor_params['editor']; ?>">
                    <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
                </div>
                <div class="buttons">
                    <span id="error_wrap"></span>
                    <span class="ctrenter_hint">ctrl+enter</span>
                    <?php echo html_button(LANG_SEND, 'send', 'icms.messages.send()'); ?>
                </div>
            </form>

        <?php } ?>

    </div>

</div>
<?php }
