<?php

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($list_header, href_to_profile($profile, ['content', $ctype['name']]));

    if ($folders && $folder_id && isset($folders[$folder_id])){
        $this->addBreadcrumb($folders[$folder_id]['title']);
    }

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $this->addToolButton(array(
            'class' => 'add',
            'icon' => 'plus-circle',
            'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
            'href'  => href_to($ctype['name'], 'add').(($folder_id  && is_numeric($folder_id) && ($user->id == $profile['id'] || $user->is_admin)) ? '?folder_id='.$folder_id : ''),
        ));

    }

    if ($folder_id  && is_numeric($folder_id) && ($user->id == $profile['id'] || $user->is_admin)){

        $this->addToolButton(array(
            'class' => 'folder_edit',
            'icon' => 'pen-square',
            'title' => LANG_EDIT_FOLDER,
            'href'  => href_to($ctype['name'], 'editfolder', $folder_id),
        ));

        $this->addToolButton(array(
            'class' => 'folder_delete',
            'icon' => 'folder-minus',
            'title' => LANG_DELETE_FOLDER,
            'href'  => href_to($ctype['name'], 'delfolder', $folder_id),
            'onclick' => "if(!confirm('".LANG_DELETE_FOLDER_CONFIRM."')){ return false; }"
        ));

    }

    if ($user->is_admin){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'icon' => 'wrench',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }
?>
<?php if(empty($hide_h1)){ ob_start(); ?>
<a class="avatar icms-user-avatar d-none d-lg-flex mr-3 <?php if (!empty($profile['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>" href="<?php echo href_to_profile($profile); ?>">
    <?php if($profile['avatar']){ ?>
        <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
    <?php } else { ?>
        <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
    <?php } ?>
</a>
<h1 class="d-flex align-items-center h2">
    <span>
        <a href="<?php echo href_to_profile($profile); ?>" class="text-muted">
            <?php html($profile['nickname']); ?>
        </a>
        <span class="d-none d-lg-inline-block"> &middot; <?php echo $list_header_h1; ?></span>
    </span>
    <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
        <sup>
            <a class="inline_rss_icon d-none d-lg-inline-block" href="<?php echo href_to('rss', 'feed', $ctype['name']) . '?user='.$profile['id']; ?>" title="RSS">
                <?php html_svg_icon('solid', 'rss'); ?>
            </a>
        </sup>
    <?php } ?>
</h1>
<?php $this->addToBlock('before_body', ob_get_clean().($toolbar_html ? html_each($toolbar_html) : '')); } ?>
<?php if (!empty($datasets) || $folders){ ?>
<div class="row align-content-end">
    <?php if (!empty($datasets)){ ?>
        <div class="col-sm">
            <?php $this->renderAsset('ui/datasets-panel', array(
                'datasets'        => $datasets,
                'dataset_name'    => $dataset,
                'current_dataset' => $current_dataset,
                'base_ds_url'     => $base_ds_url
            )); ?>
        </div>
    <?php } ?>
    <?php if ($folders){ ?>
        <div class="col-auto">
            <div class="dropdown mt-<?php echo (!empty($datasets) ? 'lg-' : ''); ?>3">
                <div class="dropdown-menu">
                    <?php foreach($folders as $folder){ ?>
                        <?php
                            $is_selected = $folder['id'] == $folder_id;
                            $url = $folder['id'] ?
                                    href_to_profile($profile, array('content', $ctype['name'], $folder['id'])) :
                                    href_to_profile($profile, array('content', $ctype['name']));
                        ?>
                        <?php if ($is_selected){ $current_folder = $folder; ?>
                            <span class="dropdown-item active"><?php echo $folder['title']; ?></span>
                        <?php } else { ?>
                            <a class="dropdown-item" href="<?php echo $url; ?>">
                                <?php echo $folder['title']; ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                    <?php echo (!empty($current_folder) ? $current_folder['title'] : LANG_ALL); ?>
                </button>
            </div>
        </div>
    <?php } ?>
</div>
<?php } ?>

<?php echo $html; ?>

<?php $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_items_html", array('user_view', $ctype, $profile, (!empty($current_folder) ? $current_folder : []))); ?>
<?php if ($hooks_html) { ?>
    <div class="sub_items_list">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>
