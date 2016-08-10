<?php

    $this->setPageTitle($group['title']);
    $this->setPageDescription($group['description'] ? string_get_meta_description($group['description']): $group['title']);

    $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
    $this->addBreadcrumb($group['title']);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<div id="group_profile">

    <div id="left_column" class="column">

		<?php if ($group['logo']) { ?>
			<div id="logo" class="block">
				<?php echo html_image($group['logo'], 'normal', $group['title']); ?>
			</div>
		<?php } ?>

        <?php if ($content_counts) { ?>
            <div class="block">
                <ul class="content_counts">
                    <?php foreach($content_counts as $ctype_name=>$count){ ?>
                        <?php if (!$count['is_in_list']) { continue; } ?>
                        <li>
                            <a href="<?php echo href_to('groups', $group['id'], array('content', $ctype_name)); ?>">
                                <?php html($count['title']); ?>
                                <span class="counter"><?php html($count['count']); ?></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <div class="block">

            <ul class="details">

                <li>
                    <strong><?php echo LANG_RATING; ?>:</strong>
                    <span class="<?php echo html_signed_class($group['rating']); ?>"><?php echo $group['rating']; ?></span>
                </li>

                <li>
                    <strong><?php echo LANG_GROUP_INFO_CREATED_DATE; ?>:</strong>
                    <?php echo string_date_age_max($group['date_pub'], true); ?>
                </li>
                <li>
                    <strong><?php echo LANG_GROUP_INFO_OWNER; ?>:</strong>
                    <a href="<?php echo href_to('users', $group['owner_id']); ?>"><?php html($group['owner_nickname']); ?></a>
                </li>
                <li>
                    <strong><?php echo LANG_GROUP_INFO_MEMBERS; ?>:</strong>
                    <a href="<?php echo $this->href_to($group['id'], 'members'); ?>"><?php echo html_spellcount($group['members_count'], LANG_GROUPS_MEMBERS_SPELLCOUNT); ?></a>
                </li>

            </ul>

        </div>

    </div>

    <div id="right_column" class="column">

        <div id="information" class="content_item block">

            <div class="group_description">
                <?php echo cmsEventsManager::hook('html_filter', $group['description']); ?>
            </div>

        </div>

    </div>

</div>

<?php if ($wall_html){ ?>
    <div id="wall_profile_wall">
        <?php echo $wall_html; ?>
    </div>
<?php } ?>

