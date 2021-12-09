<?php // Шаблон окна личных сообщений // ?>

<?php if (!$is_allowed) { ?>
    <div class="notice"><?php echo LANG_PM_NO_ACCESS; ?></div>
    <?php return; ?>
<?php } ?>

<?php $this->addTplJSNameFromContext('messages'); ?>

<script><?php
    echo $this->getLangJS('LANG_PM_DELETE_CONTACT_CONFIRM', 'LANG_PM_IGNORE_CONTACT_CONFIRM', 'LANG_YES', 'LANG_NO');
?></script>

<div id="pm_window"<?php if($is_modal){ ?> class="modal-messages"<?php } ?>
     data-contact-url="<?php echo $this->href_to('contact'); ?>"
     data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
     data-show-older-url="<?php echo $this->href_to('show_older'); ?>"
     data-ignore-url="<?php echo $this->href_to('ignore'); ?>"
     data-forgive-url="<?php echo $this->href_to('forgive'); ?>"
     data-delete-url="<?php echo $this->href_to('delete'); ?>"
     data-delete-mesage-url="<?php echo $this->href_to('delete_mesage'); ?>"
     data-restore-mesage-url="<?php echo $this->href_to('restore_mesage'); ?>"
     >

    <?php if (!$contacts) { ?>
        <div class="notice"><?php echo LANG_PM_NO_MESSAGES; ?></div>
    <?php } ?>

    <?php if ($contacts) { ?>

        <div class="layout">

            <div class="right-panel">
                <div id="user_search_panel">
                    <?php echo html_input('text', '', '', array('placeholder' => LANG_PM_USER_SEARCH)); ?>
                </div>
                <div class="contacts">
                    <?php $first_id = false; ?>
                    <?php foreach($contacts as $contact){ ?>
                        <?php $first_id = $first_id ? $first_id : $contact['id']; ?>
                        <?php $nickname = mb_strlen($contact['nickname']) > 15 ? mb_substr($contact['nickname'], 0, 15).'...' : $contact['nickname']; ?>
                        <div id="contact-<?php echo $contact['id']; ?>" class="contact" rel="<?php echo $contact['id']; ?>">
                            <a href="#<?php echo $contact['id']; ?>" onclick="return icms.messages.selectContact(<?php echo $contact['id']; ?>);" title="<?php echo $contact['nickname']; ?>">
                                <span <?php if ($contact['is_online']) { ?>class="peer_online"<?php } ?>><?php echo html_avatar_image($contact['avatar'], 'micro'); ?></span>
                                <span class="contact_nickname"><?php echo $nickname; ?></span>
                                <?php if (!$contact['is_online']) { ?>
                                    <strong title="<?php echo LANG_USERS_PROFILE_LOGDATE; ?>"><?php echo string_date_age_max($contact['date_log'], true); ?></strong>
                                <?php } ?>
                                <?php if ($contact['new_messages']) { ?>
                                    <span class="counter"><?php echo $contact['new_messages']; ?></span>
                                <?php } ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="left-panel"></div>

        </div>

        <script>
            <?php if(!$is_modal){ ?>
                icms.messages.is_modal = false;
            <?php } ?>
            icms.messages.options.refreshInterval = <?php echo $refresh_time; ?>;
            icms.messages.initUserSearch();
            <?php if($select_contact_id){ ?>
                icms.messages.selectContact(<?php echo $select_contact_id; ?>);
            <?php } else { ?>
                icms.messages.selectContact(<?php echo $first_id; ?>);
            <?php } ?>
            icms.messages.bindMyMsg();
            <?php if($is_modal){ ?>
                var resize_func = function(){
                    var pm_window = $('#pm_window:visible');
                    if ($(pm_window).length == 0){
                        $(window).off('resize', resize_func);
                        return false;
                    }
                    icms.modal.resize();
                };
                $(window).on('resize', resize_func);
                $('#pm_window').on('click', '.toogle-actions', function(){
                    $('.actions').toggleClass('actions-active');
                    $(this).toggleClass('toogle-actions-active');
                });
                icms.modal.setCallback('close', function (){
                    $('#popup-manager').removeClass('nyroModalMessage');
                });
            <?php } ?>
        </script>

    <?php } ?>

</div>