<?php $this->renderChild('group_header', array('group' => $group)); ?>

<div class="content_datasets my-3 my-md-4">
    <ul class="nav nav-pills pills-menu">
        <li class="nav-item <?php if (!$current_role_id){ ?>active<?php } ?>">
            <?php if (!$current_role_id){ ?>
                <span class="nav-link active"><?php echo LANG_ALL; ?></span>
            <?php } else { ?>
                <a class="nav-link" href="<?php echo href_to('groups', $group['slug'], 'members'); ?>"><?php echo LANG_ALL; ?></a>
            <?php } ?>
        </li>
        <?php if ($group['roles']){ ?>
            <?php foreach($group['roles'] as $role_id => $title){ ?>
                <?php $selected = ($role_id == $current_role_id); ?>
                <li class="nav-item <?php if ($selected){ ?>active<?php } ?>">
                    <?php if ($selected){ ?>
                        <span class="nav-link active"><?php echo $title; ?></span>
                    <?php } else { ?>
                        <a class="nav-link" href="<?php echo href_to('groups', $group['slug'], array('members', $role_id)); ?>"><?php echo $title; ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
        <?php } ?>
        <li <?php if ($current_role_id == -1){ ?>class="active"<?php } ?>>
            <?php if ($current_role_id == -1){ ?>
                <span class="nav-link active"><?php echo LANG_GROUPS_EDIT_STAFF; ?></span>
            <?php } else { ?>
                <a class="nav-link" href="<?php echo href_to('groups', $group['slug'], array('members', -1)); ?>"><?php echo LANG_GROUPS_EDIT_STAFF; ?></a>
            <?php } ?>
        </li>
    </ul>
</div>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>