<?php $this->addJS($this->getJavascriptFileName('groups')); ?>
<h1 id="group_profile_title">
    <?php html($group['title']); ?>
    <span>/ <?php echo LANG_GROUPS_INVITE; ?></span>
    <?php if ($group['is_closed']) { ?>
        <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
    <?php } ?>
</h1>

<div class="content_datasets">
    <ul class="pills-menu">
        <?php foreach($datasets as $set){ ?>
            <?php $ds_selected = ($dataset == $set['name']); ?>
            <li <?php if ($ds_selected){ ?>class="active"<?php } ?>>
                <?php if ($ds_selected){ ?>
                    <div><?php echo $set['title']; ?></div>
                <?php } else { ?>
                    <a href="<?php echo href_to('groups', 'invite_users', array($group['id'], $set['name'])); ?>"><?php echo $set['title']; ?></a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

<div id="ivite_users_list"><?php echo $profiles_list_html; ?></div>

<script type="text/javascript">
    function inviteFormSuccess (current_user, result){
        $(current_user).addClass('invite_sended');
        $(current_user).find('.list_actions_menu').hide();
        $(current_user).find('.actions').attr('data-notice_title', result.text);
    }
</script>