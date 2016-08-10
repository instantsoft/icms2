<?php

    $user = cmsUser::getInstance();

    $list_header = empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'];

    $this->setPageTitle($list_header, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb($list_header);

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $this->addToolButton(array(
            'class' => 'add',
            'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
            'href'  => href_to($ctype['name'], 'add'),
        ));

    }

    if ($folder_id && ($user->id == $profile['id'] || $user->is_admin)){

        $this->addToolButton(array(
            'class' => 'folder_edit',
            'title' => LANG_EDIT_FOLDER,
            'href'  => href_to($ctype['name'], 'editfolder', $folder_id),
        ));

        $this->addToolButton(array(
            'class' => 'folder_delete',
            'title' => LANG_DELETE_FOLDER,
            'href'  => href_to($ctype['name'], 'delfolder', $folder_id),
            'onclick' => "if(!confirm('".LANG_DELETE_FOLDER_CONFIRM."')){ return false; }"
        ));

    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }

    $rss_query = "?user={$profile['id']}";

?>

<h1 id="user_profile_title">

    <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
        <div class="content_list_rss_icon">
            <a href="<?php echo href_to('rss', 'feed', $ctype['name']) . $rss_query; ?>">RSS</a>
        </div>
    <?php } ?>

    <div class="avatar">
        <a href="<?php echo $this->href_to($profile['id']); ?>"><?php echo html_avatar_image($profile['avatar'], 'micro', $profile['nickname']); ?></a>
    </div>

    <div class="name">
        <a href="<?php echo $this->href_to($profile['id']); ?>"><?php html($profile['nickname']); ?></a> /
        <span><?php echo $list_header; ?></span>
    </div>

</h1>

<?php if ($folders){ ?>
    <div id="user_content_folders">
        <ul class="pills-menu-small">
            <?php foreach($folders as $folder){ ?>
                <?php
                    $is_selected = $folder['id'] == $folder_id;
                    $url = $folder['id'] ?
                                $this->href_to($profile['id'], array('content', $ctype['name'], $folder['id'])) :
                                $this->href_to($profile['id'], array('content', $ctype['name']));
                ?>
                <li <?php if ($is_selected){ ?>class="active"<?php } ?>>
                    <?php if ($is_selected){ ?>
                        <div><?php echo $folder['title']; ?></div>
                    <?php } else { ?>
                        <a href="<?php echo $url; ?>"><?php echo $folder['title']; ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div id="user_content_list">
    <?php echo $html; ?>
</div>