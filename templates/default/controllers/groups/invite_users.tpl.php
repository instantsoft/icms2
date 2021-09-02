<?php $this->addTplJSName('groups'); ?>
<h1 id="group_profile_title">
    <?php html($group['title']); ?>
    <span>/ <?php echo LANG_GROUPS_INVITE; ?></span>
    <?php if ($group['is_closed']) { ?>
        <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
    <?php } ?>
</h1>

<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'     => $datasets,
        'dataset_name' => $dataset,
        'base_ds_url'  => href_to('groups', 'invite_users', array($group['id'])) . '%s'
    ));
} ?>

<div id="ivite_users_list"><?php echo $profiles_list_html; ?></div>

<script>
    function inviteFormSuccess (current_user, result){
        $(current_user).addClass('invite_sended');
        $(current_user).find('.list_actions_menu').hide();
        $(current_user).find('.actions').attr('data-notice_title', result.text);
    }
</script>