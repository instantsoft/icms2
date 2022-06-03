<?php if(!$is_modal){

    $this->setPageTitle(LANG_PM_MY_MESSAGES);
    $this->addBreadcrumb(LANG_PM_MY_MESSAGES);

} ?>

<?php $this->addTplJSNameFromContext('messages'); ?>

<?php if (!$is_allowed) { ?>
    <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_ACCESS; ?></div>
    <?php return; ?>
<?php } ?>

<?php if (!$contacts) { ?>
    <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_MESSAGES; ?></div>
    <?php return; ?>
<?php } ?>

<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_PM_DELETE_CONTACT_CONFIRM', 'LANG_PM_IGNORE_CONTACT_CONFIRM', 'LANG_YES', 'LANG_NO'); ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>

<div id="pm_window" class="icms-messages<?php if($is_modal){ ?> modal-messages<?php } else { ?> mb-3 mb-lg-4<?php } ?>"
     data-audio-base-url="<?php echo $this->getTemplateFilePath('audio'); ?>/"
     data-contact-url="<?php echo $this->href_to('contact'); ?>"
     data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
     data-show-older-url="<?php echo $this->href_to('show_older'); ?>"
     data-ignore-url="<?php echo $this->href_to('ignore'); ?>"
     data-forgive-url="<?php echo $this->href_to('forgive'); ?>"
     data-delete-url="<?php echo $this->href_to('delete'); ?>"
     data-delete-mesage-url="<?php echo $this->href_to('delete_mesage'); ?>"
     data-restore-mesage-url="<?php echo $this->href_to('restore_mesage'); ?>"
     >

    <div class="layout row no-gutters">

        <div class="right-panel col-lg-3 border-right d-none d-lg-block">
            <div class="sticky-top icms-messages__contacts-block">
                <div id="user_search_panel" class="bg-gray p-2 border-bottom pannel-toolbar">
                    <?php echo html_input('text', '', '', array('placeholder' => LANG_PM_USER_SEARCH)); ?>
                </div>
                <div class="contacts icms-messages__contacts-list list-group" id="contacts-list">
                    <?php foreach($contacts as $contact){ ?>
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
                                <small class="d-block text-muted">
                                    <?php echo html_date_time($contact['date_last_msg']); ?>
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

        <div class="left-panel col-lg-9 d-lg-block">
            <div class="h-100 text-muted d-flex align-items-center justify-content-center bg-light">
                <span class="h3 m-0"><?php echo LANG_PM_SELECT_CONTACT; ?></span>
            </div>
        </div>

    </div>

    <?php ob_start(); ?>
    <script>
        <?php if(!$is_modal){ ?>
            icms.messages.is_modal = false;
        <?php } ?>
        icms.messages.options.refreshInterval = <?php echo $refresh_time; ?>;
        $(function(){
            icms.messages.initUserSearch();
            <?php if($select_contact_id){ ?>
                icms.messages.selectContact(<?php echo $select_contact_id; ?>);
            <?php } else { ?>
                $('.left-panel').addClass('d-none');
                $('.right-panel').removeClass('d-none');
            <?php } ?>
            icms.messages.bindMyMsg();
        });
        <?php if($is_modal){ ?>
            $('#icms_modal .modal-dialog').addClass('modal-xl modal-dialog-icms-messages');
            icms.modal.setCallback('close', function (){
                $('#icms_modal .modal-dialog').removeClass('modal-xl modal-dialog-icms-messages');
            });
        <?php } ?>
    </script>
    <?php $this->addBottom(ob_get_clean()); ?>

</div>