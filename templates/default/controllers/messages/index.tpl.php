<?php // Шаблон окна личных сообщений // ?>

<?php if (!$is_allowed) { ?>
    <div class="notice"><?php echo LANG_PM_NO_ACCESS; ?></div>
    <?php return; ?>
<?php } ?>

<script><?php
    echo $this->getLangJS('LANG_PM_DELETE_CONTACT_CONFIRM', 'LANG_PM_IGNORE_CONTACT_CONFIRM');
?></script>

<div id="pm_window"
     data-contact-url="<?php echo $this->href_to('contact'); ?>"
     data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
     data-show-older-url="<?php echo $this->href_to('show_older'); ?>"
     data-ignore-url="<?php echo $this->href_to('ignore'); ?>"
     data-forgive-url="<?php echo $this->href_to('forgive'); ?>"
     data-delete-url="<?php echo $this->href_to('delete'); ?>"
     >

    <?php if (!$contacts) { ?>
        <div class="notice"><?php echo LANG_PM_NO_MESSAGES; ?></div>
    <?php } ?>

    <?php if ($contacts) { ?>

        <div class="layout">

            <div class="right-panel">
                <div class="contacts">
                    <?php $first_id = false; ?>
                    <?php foreach($contacts as $contact){ ?>
                        <?php $first_id = $first_id ? $first_id : $contact['id']; ?>
                        <?php $nickname = mb_strlen($contact['nickname']) > 15 ? mb_substr($contact['nickname'], 0, 15).'...' : $contact['nickname']; ?>
                        <div id="contact-<?php echo $contact['id']; ?>" class="contact" rel="<?php echo $contact['id']; ?>">
                            <a href="#<?php echo $contact['id']; ?>" onclick="icms.messages.selectContact(<?php echo $contact['id']; ?>)" title="<?php echo $contact['nickname']; ?>">
                                <span><?php echo html_avatar_image($contact['avatar'], 'micro'); ?></span>
                                <span><?php echo $nickname; ?></span>
                                <?php if ($contact['new_messages']) { ?>
                                    <span class="counter"><?php echo $contact['new_messages']; ?></span>
                                <?php } ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="left-panel loading-panel">

            </div>

        </div>

        <script>icms.messages.selectContact(<?php echo $first_id; ?>)</script>

    <?php } ?>

</div>
