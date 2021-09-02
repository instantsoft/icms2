<?php

    $user = cmsUser::getInstance();

    $this->addMenuItems('group_tabs', $this->controller->getGroupTabs($group));
    $this->addMenuItems('controller_actions_menu', $this->controller->getToolButtons($group));

    $this->setPagePatternTitle($group);
    if(!empty($filter_titles)){ $this->addToPageTitle(implode(', ', $filter_titles)); }
    $this->setPagePatternDescription($group);

?>

<?php if(!empty($group['fields']['cover']['is_in_item']) && $group['cover']){ ?>
<div style="background-image: url(<?php echo html_image_src($group['cover'], $group['fields']['cover']['handler']->getOption('size_full'), true); ?>);" id="group_head">
    <div class="gwrapper">
        <div class="group_counts">
            <div>
                <strong><?php echo LANG_RATING; ?>:</strong>
                <span><?php echo $group['rating']; ?></span>
            </div>
            <div>
                <strong><?php echo LANG_GROUP_INFO_CREATED_DATE; ?>:</strong>
                <span><?php echo string_date_age_max($group['date_pub'], true); ?></span>
            </div>
            <div>
                <strong><?php echo LANG_GROUP_INFO_OWNER; ?>:</strong>
                <span><a href="<?php echo href_to_profile($group['owner']); ?>"><?php html($group['owner_nickname']); ?></a></span>
            </div>
        </div>
        <div class="share">
            <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-title="<?php html($group['title']); ?><?php if (!empty($group['sub_title'])) { ?> / <?php html($group['sub_title']); ?><?php } ?>" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,viber,whatsapp" data-description="<?php html(string_short($group['description'], 200)); ?>"></div>
        </div>
    </div>
</div>
<?php } ?>
<?php $this->actionsToolbar(LANG_GROUPS_MENU); ?>
<h1 id="group_profile_title">
	<?php if (!empty($group['fields']['logo']['is_in_item']) && $group['logo']){ ?>
		<span class="logo"><?php echo html_image($group['logo'], $group['fields']['logo']['handler']->getOption('size_full'), $group['title']); ?></span>
	<?php } ?>
    <?php if (!empty($group['fields']['title']['is_in_item'])){ ?>
        <?php if (!empty($this->controller->options['tag_h1'])) { ?>
            <?php echo string_replace_keys_values_extended($this->controller->options['tag_h1'], $group); ?>
        <?php } else { ?>
            <?php html($group['title']); ?>
        <?php } ?>
        <?php if (!empty($group['sub_title'])) { ?>
            <span>/ <?php html($group['sub_title']); ?></span>
        <?php } ?>
    <?php } ?>
    <?php if ($group['is_closed']) { ?>
        <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
    <?php } ?>
</h1>

<?php if (!$group['is_closed'] || ($group['access']['is_member'] || $user->is_admin)){ ?>
    <div id="group_profile_tabs">
        <div class="tabs-menu">
            <?php $this->menu('group_tabs', true, 'tabbed', 6); ?>
        </div>
    </div>
<?php } ?>