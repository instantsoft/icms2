<?php // Шаблон панели диалога // ?>

<?php if ($contact){ ?>
<div id="pm_contact">

    <div class="border-bottom py-2 pl-md-2 d-flex align-items-center icms-messages-toolbar pannel-toolbar sticky-top bg-white">
        <div class="icms-messages-toolbar__info d-flex w-100 align-items-center">
            <button id="contact_toggle" class="btn btn-info mr-2 d-block d-md-none">
                <?php html_svg_icon('solid', 'chevron-left'); ?>
            </button>
            <a href="<?php echo href_to_profile($contact); ?>" class="icms-user-avatar mr-2 small <?php if (!empty($contact['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                <?php if($contact['avatar']){ ?>
                    <?php echo html_avatar_image($contact['avatar'], 'micro', $contact['nickname']); ?>
                <?php } else { ?>
                    <?php echo html_avatar_image_empty($contact['nickname'], 'avatar__mini'); ?>
                <?php } ?>
            </a>
            <span class="contact_nickname">
                <?php echo $contact['nickname']; ?>
                <small title="<?php echo LANG_USERS_PROFILE_LOGDATE; ?>" class="user_date_log text-muted d-block">
                     <?php if (!$contact['is_online']) { ?>
                        <?php echo string_date_age_max($contact['date_log'], true); ?>
                    <?php } else { ?>
                        <?php echo LANG_ONLINE; ?>
                    <?php } ?>
                </small>
            </span>
            <div class="actions ml-auto">
                <div class="dropdown">
                    <button class="btn" type="button" data-toggle="dropdown">
                        <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php if (!$contact['is_admin'] && !$contact['is_ignored']){ ?>
                            <a class="dropdown-item" href="#" onclick="return icms.messages.ignoreContact(<?php echo $contact['id']; ?>);">
                                <?php echo LANG_PM_ACTION_IGNORE; ?>
                            </a>
                        <?php } ?>
                        <a class="dropdown-item" href="#" onclick="return icms.messages.deleteContact(<?php echo $contact['id']; ?>);">
                            <?php echo LANG_PM_DELETE_CONTACT; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="icms-messages-toolbar__action d-none w-100 align-items-center">
            <?php echo html_button(LANG_DELETE, 'delete_msgs', 'icms.messages.deleteMsgs()', ['class'=>'btn-sm btn-primary', 'id' => 'delete_msgs']); ?>
            <?php echo html_button(LANG_CANCEL, 'delete_msgs', '', ['class'=>'btn-sm btn-secondary ml-auto', 'id' => 'cancel_msgs']); ?>
        </div>
    </div>

    <div id="pm_chat" class="icms-messages-chat bg-gray border-bottom overflow-hidden text-center">
        <?php if($has_older){ ?>
            <a class="show-older btn btn-secondary mt-2 mb-0" href="#show-older" onclick="return icms.messages.showOlder(<?php echo $contact['id'] ?>, this);" rel="<?php echo $messages[0]['id']; ?>">
                <span><?php echo LANG_PM_SHOW_OLDER_MESSAGES; ?></span>
            </a>
        <?php } ?>

        <?php if ($messages){ ?>
            <?php echo $this->renderChild('message', ['messages'=>$messages, 'user'=>$user, 'last_date' => '']); ?>
        <?php } ?>
    </div>

    <div class="composer py-2 px-0 p-md-2">

        <?php if ($contact['is_ignored']){ ?>

            <div class="ignored_info alert alert-warning m-0 rounded-0 border-top-0 border-left-0 m-n2">
                <?php echo LANG_PM_CONTACT_IS_IGNORED; ?>
                <?php echo html_button(LANG_PM_ACTION_FORGIVE, 'forgive', 'icms.messages.forgiveContact('.$contact['id'].')', ['class'=>'btn-sm btn-secondary']); ?>
            </div>

        <?php } else if ($is_me_ignored){ ?>

            <div class="ignored_info alert alert-warning m-0 rounded-0 border-top-0 border-left-0 m-n2"><?php echo LANG_PM_YOU_ARE_IGNORED; ?></div>

        <?php } else if ($is_private){ ?>

            <div class="ignored_info alert alert-warning m-0 rounded-0 border-top-0 border-left-0 m-n2"><?php echo LANG_PM_CONTACT_IS_PRIVATE; ?></div>

        <?php } else { ?>

            <form action="<?php echo $this->href_to('send'); ?>" method="post" class="position-relative">
                <?php echo html_input('hidden', 'last_date', '', array('id' => 'msg_last_date')); ?>
                <?php echo html_input('hidden', 'contact_id', $contact['id']); ?>
                <?php echo html_csrf_token(); ?>
                <div class="editor editor-<?php echo $editor_params['editor']; ?>">
                    <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
                </div>
                <div class="buttons d-flex justify-content-between align-items-center mt-2">
                    <span>
                        <span class="bg-danger text-white px-3 py-2" id="error_wrap" style="display: none"></span>
                    </span>
                    <span>
                        <span class="ctrenter_hint text-muted mr-2">ctrl+enter</span>
                        <?php echo html_button(LANG_SEND, 'send', 'icms.messages.send()', ['class'=>'btn-primary']); ?>
                    </span>
                </div>
            </form>

        <?php } ?>

    </div>

</div>
<?php }
