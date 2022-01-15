<?php // Шаблон панели диалога // ?>

<?php if ($contact){ ?>
<div id="pm_contact">

    <div class="border-bottom py-2 px-lg-2 d-flex align-items-center icms-messages-toolbar pannel-toolbar sticky-top bg-white">
        <div class="icms-messages-toolbar__info d-flex w-100 align-items-center">
            <button id="contact_toggle" class="btn mr-2 d-block d-lg-none">
                <?php html_svg_icon('solid', 'chevron-left'); ?>
            </button>
            <a href="<?php echo href_to_profile($contact); ?>" class="text-decoration-none d-flex align-items-center">
                <span class="icms-user-avatar mr-2 small <?php if (!empty($contact['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                    <?php if($contact['avatar']){ ?>
                        <?php echo html_avatar_image($contact['avatar'], 'micro', $contact['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($contact['nickname'], 'avatar__mini'); ?>
                    <?php } ?>
                </span>
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
            </a>
            <div class="actions d-flex align-items-center ml-auto">
                <div class="dropdown">
                    <button class="btn btn-dylan" type="button" data-toggle="dropdown">
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
                <button type="button" class="btn d-block d-sm-none" title="<?php echo LANG_CLOSE; ?>" onclick="return icms.modal.close();">
                    <?php html_svg_icon('solid', 'times'); ?>
                </button>
            </div>
        </div>
        <div class="icms-messages-toolbar__action d-none w-100 align-items-center px-2 px-lg-0">
            <?php echo html_button(LANG_DELETE, 'delete_msgs', 'icms.messages.deleteMsgs()', ['class'=>'btn-sm btn-primary', 'id' => 'delete_msgs']); ?>
            <?php echo html_button(LANG_CANCEL, 'delete_msgs', '', ['class'=>'btn-sm btn-secondary ml-auto', 'id' => 'cancel_msgs']); ?>
        </div>
    </div>

    <div id="pm_chat" class="icms-messages-chat bg-gray border-bottom text-center d-flex flex-column">
        <?php if($has_older){ ?>
            <a class="show-older btn btn-secondary mt-2 mb-0" href="#show-older" onclick="return icms.messages.showOlder(<?php echo $contact['id'] ?>, this);" rel="<?php echo $messages[0]['id']; ?>">
                <span><?php echo LANG_PM_SHOW_OLDER_MESSAGES; ?></span>
            </a>
        <?php } ?>
            <div class="mt-auto"></div>

        <?php if ($messages){ ?>
            <?php echo $this->renderChild('message', ['messages'=>$messages, 'user'=>$user, 'last_date' => '']); ?>
        <?php } ?>
    </div>

    <div class="composer py-2 px-0 p-lg-2">

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

            <form action="<?php echo $this->href_to('send'); ?>" method="post" class="position-relative px-2 px-lg-0">
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
                        <button class="button btn btn-primary" value="1" name="send" onclick="icms.messages.send()" type="button">
                            <span>
                                <?php html_svg_icon('solid', 'paper-plane'); ?>
                                <?php echo LANG_SEND; ?>
                            </span>
                        </button>
                    </span>
                </div>
            </form>

        <?php } ?>

    </div>

</div>
<?php }
