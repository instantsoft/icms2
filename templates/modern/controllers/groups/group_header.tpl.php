<?php

    $user = cmsUser::getInstance();

    $this->addMenuItems('group_tabs', $this->controller->getGroupTabs($group));
    $this->addMenuItems('toolbar', $this->controller->getToolButtons($group));

    $this->setPagePatternTitle($group);
    if(!empty($filter_titles)){ $this->addToPageTitle(implode(', ', $filter_titles)); }
    $this->setPagePatternDescription($group);

?>

<?php if(!empty($group['fields']['cover']['is_in_item']) && $group['cover']){ ?>
<div class="embed-responsive embed-responsive-36by9 icms-bg__cover mb-3" style="background-image: url(<?php echo html_image_src($group['cover'], $group['fields']['cover']['handler']->getOption('size_full'), true); ?>);">
    <div class="icms-groups-g__header_counts px-3 py-2 position-absolute text-white small d-flex">
        <div title="<?php echo LANG_RATING; ?>" data-toggle="tooltip" data-placement="top">
            <?php html_svg_icon('solid', 'star'); ?>
            <span><?php echo $group['rating']; ?></span>
        </div>
        <div class="ml-3" title="<?php echo LANG_GROUP_INFO_CREATED_DATE; ?>" data-toggle="tooltip" data-placement="top">
            <?php html_svg_icon('solid', 'calendar-alt'); ?>
            <span><?php echo string_date_age_max($group['date_pub'], true); ?></span>
        </div>
        <div class="ml-3" title="<?php echo LANG_GROUP_INFO_OWNER; ?>" data-toggle="tooltip" data-placement="top">
            <?php html_svg_icon('solid', 'user'); ?>
            <a class="text-white" href="<?php echo href_to('users', $group['owner_id']); ?>">
                <?php html($group['owner_nickname']); ?>
            </a>
        </div>
    </div>
</div>
<?php } ?>

<h1 class="d-flex align-items-center">
	<?php if (!empty($group['fields']['logo']['is_in_item']) && $group['logo']){ ?>
		<span class="avatar icms-user-avatar d-flex mr-3">
            <?php echo html_image($group['logo'], $group['fields']['logo']['handler']->getOption('size_full'), $group['title']); ?>
        </span>
	<?php } ?>
    <?php if (!empty($group['fields']['title']['is_in_item'])){ ?>
        <span>
            <?php if (!empty($this->controller->options['tag_h1'])) { ?>
                <?php echo string_replace_keys_values_extended($this->controller->options['tag_h1'], $group); ?>
            <?php } else { ?>
                <?php html($group['title']); ?>
            <?php } ?>
            <?php if (!empty($group['sub_title'])) { ?>
                <span class="d-none d-lg-inline-block text-muted"> &middot; <?php html($group['sub_title']); ?></span>
            <?php } ?>
        </span>
    <?php } ?>
    <?php if ($group['is_closed']) { ?>
        <span class="is_closed text-muted ml-2" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>">
            <?php html_svg_icon('solid', 'lock'); ?>
        </span>
    <?php } ?>
</h1>

<?php if (!$group['is_closed'] || ($group['access']['is_member'] || $user->is_admin)){ ?>
<div class="mobile-menu-wrapper mobile-menu-wrapper__tab mt-3">
    <?php $this->menu('group_tabs', true, 'icms-groups__tabs nav nav-tabs', 6); ?>
</div>
<?php } ?>