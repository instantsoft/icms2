<?php

    $user = cmsUser::getInstance();

    if(!isset($content_counts)) {
        $content_counts = $this->controller->model->getGroupContentCounts($group['id']);
    }

    $group['content_count'] = 0;
    $group['first_ctype_name'] = false;

    if ($content_counts){
        foreach($content_counts as $ctype_name=>$count){
            if (!$count['is_in_list']) { continue; }
            if (!$group['first_ctype_name']) { $group['first_ctype_name'] = $ctype_name; }
            $group['content_count'] += $count['count'];
        }
    }

    $this->addMenuItems('group_tabs', $this->controller->getGroupTabs($group));

    $is_owner = $user->id == $group['owner_id'];
    $membership = $this->controller->model->getMembership($group['id'], $user->id);
    $is_member = ($membership !== false);
    $member_role = $is_member ? $membership['role'] : groups::ROLE_NONE;
    $invite = $this->controller->model->getInvite($group['id'], $user->id);
    $is_can_invite = ($is_member && ($group['join_policy'] != groups::JOIN_POLICY_PRIVATE)) || $is_owner;

    if (($member_role == groups::ROLE_STAFF && $group['edit_policy'] == groups::EDIT_POLICY_STAFF) || $is_owner || cmsUser::isAllowed('groups', 'edit', 'all')){
        $this->addToolButton(array(
            'title' => LANG_GROUPS_EDIT,
            'class' => 'settings',
            'href' => $this->href_to($group['id'], 'edit')
        ));
    }

    if ($user->id && $is_can_invite){
        $this->addToolButton(array(
            'title' => LANG_GROUPS_INVITE,
            'class' => 'group_add ajax-modal',
            'href' => $this->href_to('invite_friends', $group['id'])
        ));
    }

    if ($user->id && !$is_member && ($group['join_policy'] == groups::JOIN_POLICY_FREE || $invite)){
        $this->addToolButton(array(
            'title' => LANG_GROUPS_JOIN,
            'class' => 'user_add',
            'href' => $this->href_to($group['id'], 'join'),
            'confirm' => LANG_GROUPS_JOIN . '?'
        ));
    }

    if ($user->id && $is_member && !$is_owner){
        $this->addToolButton(array(
            'title' => LANG_GROUPS_LEAVE,
            'class' => 'user_delete',
            'href' => $this->href_to($group['id'], 'leave'),
            'confirm' => LANG_GROUPS_LEAVE . '?'
        ));
    }

    if (cmsUser::isAllowed('groups', 'delete', 'all') || (cmsUser::isAllowed('groups', 'delete', 'own') && $is_owner)){
        $this->addToolButton(array(
            'title' => LANG_GROUPS_DELETE,
            'class' => 'delete ajax-modal',
            'href' => $this->href_to($group['id'], 'delete')
        ));
    }

?>

<h1 id="group_profile_title">
	<?php if ($group['logo']){ ?>
		<span class="logo"><?php echo html_image($group['logo'], 'micro', $group['title']); ?></span>
	<?php } ?>
    <?php html($group['title']); ?>
    <?php if ($group['is_closed']) { ?>
        <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
    <?php } ?>
</h1>

<?php if (!$group['is_closed'] || ($is_member || $user->is_admin)){ ?>

    <div id="group_profile_tabs">
        <div class="tabs-menu">
            <?php $this->menu('group_tabs', true, 'tabbed'); ?>
        </div>
    </div>

<?php } ?>
