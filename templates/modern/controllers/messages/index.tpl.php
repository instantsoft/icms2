<?php if(!$is_modal){

    $this->setPageTitle(LANG_PM_MY_MESSAGES);
    $this->addBreadcrumb(LANG_PM_MY_MESSAGES);

} ?>

<?php $this->addTplJSNameFromContext('messages'); ?>

<?php if (!$is_allowed) { ?>
    <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_ACCESS; ?></div>
    <?php return; ?>
<?php } ?>

<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_PM_DELETE_CONTACT_CONFIRM', 'LANG_PM_IGNORE_CONTACT_CONFIRM', 'LANG_YES', 'LANG_NO'); ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>

<div id="pm_window" class="icms-messages<?php if($is_modal){ ?> modal-messages m-n3<?php } else { ?> mb-3 mb-md-4<?php } ?>"
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
        <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_MESSAGES; ?></div>
    <?php } ?>

    <?php if ($contacts) { ?>

        <div class="layout row no-gutters">

            <div class="right-panel col-md-3 border-right d-none d-md-block">
                <div class="sticky-top">
                    <div id="user_search_panel" class="bg-gray p-2 border-bottom pannel-toolbar">
                        <?php echo html_input('text', '', '', array('placeholder' => LANG_PM_USER_SEARCH)); ?>
                    </div>
                    <div class="contacts list-group">
                        <?php $first_id = false; ?>
                        <?php foreach($contacts as $contact){ ?>
                            <?php $first_id = $first_id ? $first_id : $contact['id']; ?>
                            <?php $nickname = mb_strlen($contact['nickname']) > 15 ? mb_substr($contact['nickname'], 0, 15).'...' : $contact['nickname']; ?>
                            <a id="contact-<?php echo $contact['id']; ?>" href="#<?php echo $contact['id']; ?>" class="text-decoration-none d-flex align-items-center contact list-group-item border-0 rounded-0 p-2" onclick="return icms.messages.selectContact(<?php echo $contact['id']; ?>);" title="<?php echo $contact['nickname']; ?>" rel="<?php echo $contact['id']; ?>">

                                <span class="icms-user-avatar mr-2 small <?php if (!empty($contact['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>" rel="<?php echo $contact['id']; ?>">
                                    <?php if($contact['avatar']){ ?>
                                        <?php echo html_avatar_image($contact['avatar'], 'micro', $contact['nickname']); ?>
                                    <?php } else { ?>
                                        <?php echo html_avatar_image_empty($contact['nickname'], 'avatar__mini'); ?>
                                    <?php } ?>
                                </span>

                                <span class="contact_nickname">
                                    <span><?php echo $nickname; ?></span>
                                    <small title="<?php echo LANG_USERS_PROFILE_LOGDATE; ?>" class="d-block text-muted">
                                        <?php if (!$contact['is_online']) { ?>
                                           <?php echo string_date_age_max($contact['date_log'], true); ?>
                                       <?php } else { ?>
                                           <?php echo LANG_ONLINE; ?>
                                       <?php } ?>
                                    </small>
                                </span>
                                <?php if ($contact['new_messages']) { ?>
                                    <span class="counter ml-auto badge badge-pill badge-danger">
                                        <?php echo $contact['new_messages']; ?>
                                    </span>
                                <?php } ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="left-panel col-md-9"></div>

        </div>

        <?php ob_start(); ?>
        <script type="text/javascript">
            <?php if(!$is_modal){ ?>
                icms.messages.is_modal = false;
            <?php } ?>
            icms.messages.options.refreshInterval = <?php echo $refresh_time; ?>;
            icms.messages.initUserSearch();
            icms.messages.selectContact(<?php echo $first_id; ?>);
            icms.messages.bindMyMsg();
            <?php if($is_modal){ ?>
                $('#icms_modal .modal-dialog').addClass('modal-xl');
                icms.modal.setCallback('close', function (){
                    $('#icms_modal .modal-dialog').removeClass('modal-xl');
                });
            <?php } ?>
        </script>
        <?php $this->addBottom(ob_get_clean()); ?>

    <?php } ?>

</div>