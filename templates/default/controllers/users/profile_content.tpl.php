<?php

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($list_header, href_to_profile($profile, array('content', $ctype['name'])));

    if ($folders && $folder_id && isset($folders[$folder_id])){

        $this->addBreadcrumb($folders[$folder_id]['title']);

        $this->setPageTitle($list_header, implode(', ', $filter_titles), $folders[$folder_id]['title'], $profile['nickname']);
        $this->setPageDescription($profile['nickname'].' — '.$list_header.' '.$folders[$folder_id]['title']);

    } else {

        $this->setPageTitle($list_header, implode(', ', $filter_titles), $profile['nickname']);
        $this->setPageDescription($profile['nickname'].' — '.$list_header);

    }

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $this->addToolButton(array(
            'class' => 'add',
            'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
            'href'  => href_to($ctype['name'], 'add').(($folder_id  && is_numeric($folder_id) && ($user->id == $profile['id'] || $user->is_admin)) ? '?folder_id='.$folder_id : ''),
        ));

    }

    if ($folder_id  && is_numeric($folder_id) && ($user->id == $profile['id'] || $user->is_admin)){

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

    if ($user->is_admin){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }
    if ($toolbar_html) {
        echo html_each($toolbar_html);
    }
?>

<h1 id="user_profile_title">

    <div class="avatar">
        <a href="<?php echo href_to_profile($profile); ?>"><?php echo html_avatar_image($profile['avatar'], 'micro', $profile['nickname'], $profile['is_deleted']); ?></a>
    </div>

    <div class="name">
        <a href="<?php echo href_to_profile($profile); ?>"><?php html($profile['nickname']); ?></a> /
        <span><?php echo $list_header; ?></span>
        <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
            <a class="inline_rss_icon" href="<?php echo href_to('rss', 'feed', $ctype['name']) . '?user='.$profile['id']; ?>">RSS</a>
        <?php } ?>
    </div>

</h1>

<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>

<?php if ($folders){ ?>
    <div id="user_content_folders">
        <ul class="pills-menu-small">
            <?php foreach($folders as $folder){ ?>
                <?php
                    $is_selected = $folder['id'] == $folder_id;
                    $url = $folder['id'] ?
                                href_to_profile($profile, array('content', $ctype['name'], $folder['id'])) :
                                href_to_profile($profile, array('content', $ctype['name']));
                ?>
                <li <?php if ($is_selected){ ?>class="active"<?php } ?>>
                    <?php if ($is_selected){ $current_folder = $folder; ?>
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

<?php $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_items_html", array('user_view', $ctype, $profile, (!empty($current_folder) ? $current_folder : array()))); ?>
<?php if ($hooks_html) { ?>
    <div class="sub_items_list">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>