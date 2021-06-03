<?php $this->addTplJSName('jquery-scroll'); ?>
<?php $this->addTplJSName('wall'); ?>
<a name="wall"></a>
<div id="wall_widget">

    <div class="title_bar">
        <h2 class="title"><?php echo $title; ?></h2>
        <?php if ($permissions['add']){ ?>
            <a href="#wall-write" id="wall_add_link" onclick="return icms.wall.add()">
                <?php echo LANG_WALL_ENTRY_ADD; ?>
            </a>
        <?php } ?>
    </div>

    <div id="wall_urls" style="display: none"
            data-get-url="<?php echo $this->href_to('get'); ?>"
            data-replies-url="<?php echo $this->href_to('get_replies'); ?>"
            data-delete-url="<?php echo $this->href_to('delete'); ?>"
    ></div>

    <div id="wall_add_form">
        <div class="preview_box"></div>
        <form action="<?php echo $this->href_to('submit'); ?>" method="post">
            <?php echo html_csrf_token(); ?>
            <?php echo html_input('hidden', 'action', 'add'); ?>
            <?php echo html_input('hidden', 'id', 0); ?>
            <?php echo html_input('hidden', 'parent_id', 0); ?>
            <?php echo html_input('hidden', 'pc', $controller); ?>
            <?php echo html_input('hidden', 'pt', $profile_type); ?>
            <?php echo html_input('hidden', 'pi', $profile_id); ?>
            <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
            <div class="buttons">
                <?php echo html_button(LANG_PREVIEW, 'preview', 'icms.wall.preview()', array('class'=>'button-preview')); ?>
                <?php echo html_button(LANG_SEND, 'submit', 'icms.wall.submit()'); ?>
                <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.wall.restoreForm()', array('class'=>'button-cancel')); ?>
            </div>
            <div class="loading">
                <?php echo LANG_LOADING; ?>
            </div>
        </form>
    </div>

    <div id="entries_list">

        <?php if (!$entries) { ?>
            <p class="no_entries">
                <?php if ($permissions['add']){ ?>
                    <?php echo LANG_WALL_EMPTY; ?>
                <?php } else { ?>
                    <?php echo LANG_WALL_EMPTY_ONLY; ?>
                <?php } ?>
            </p>
        <?php } ?>

        <?php if ($entries){ ?>
            <?php
                echo $this->renderChild('entry', array(
                    'entries'     => $entries,
                    'max_entries' => $max_entries,
                    'page'        => $page,
                    'user'        => $user,
                    'permissions' => $permissions
                ));
            ?>
        <?php } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <div class="wall_pages" <?php if($max_entries && (count($entries) > $max_entries) && $page==1) {?>style="display:none"<?php } ?>>
            <?php echo html_pagebar($page, $perpage, $total, '#wall'); ?>
        </div>
    <?php } ?>

    <script>
        <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_WALL_ENTRY_DELETED', 'LANG_WALL_ENTRY_DELETE_CONFIRM'); ?>
        <?php if ($show_id) { ?>
            <?php if ($go_reply && !$user->is_logged) { $go_reply = false; } ?>
            icms.wall.show(<?php echo $show_id; ?>, <?php echo $show_reply_id; ?>, <?php echo $go_reply; ?>);
        <?php } ?>
    </script>

</div>