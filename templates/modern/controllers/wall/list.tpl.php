<?php $this->addTplJSName('jquery-scroll'); ?>
<?php $this->addTplJSName('wall'); ?>
<div id="wall_widget" class="border-top mt-3 pt-3">

    <div class="title_bar d-flex justify-content-between">
        <h3 class="m-0"><?php echo $title; ?></h3>
        <?php if ($permissions['add']){ ?>
            <a href="#wall-write" id="wall_add_link" onclick="return icms.wall.add()" class="btn btn-primary">
                <?php html_svg_icon('solid', 'marker'); ?>
                <span class="d-none d-md-inline-block"><?php echo LANG_WALL_ENTRY_ADD; ?></span>
            </a>
        <?php } ?>
    </div>

    <div id="wall_urls" style="display: none"
            data-get-url="<?php echo $this->href_to('get'); ?>"
            data-replies-url="<?php echo $this->href_to('get_replies'); ?>"
            data-delete-url="<?php echo $this->href_to('delete'); ?>"
    ></div>

    <div id="wall_add_form" class="mb-3">
        <div class="preview_box alert alert-light border mt-3 d-none"></div>
        <form action="<?php echo $this->href_to('submit'); ?>" method="post">
            <?php echo html_csrf_token(); ?>
            <?php echo html_input('hidden', 'action', 'add'); ?>
            <?php echo html_input('hidden', 'id', 0); ?>
            <?php echo html_input('hidden', 'parent_id', 0); ?>
            <?php echo html_input('hidden', 'pc', $controller); ?>
            <?php echo html_input('hidden', 'pt', $profile_type); ?>
            <?php echo html_input('hidden', 'pi', $profile_id); ?>
            <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
            <div class="buttons row justify-content-between">
                <div class="col">
                    <?php echo html_button(LANG_SEND, 'submit', 'icms.wall.submit()', ['class' => 'button-add button-update btn-primary']); ?>
                    <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.wall.restoreForm()', ['class'=>'btn-secondary button-cancel']); ?>
                </div>
                <div class="col-auto">
                    <button class="button btn button-preview btn-info" name="preview" onclick="icms.wall.preview()" type="button">
                        <?php html_svg_icon('solid', 'eye'); ?>
                        <span class="d-none d-lg-inline-block"><?php echo LANG_PREVIEW; ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="entries_list" class="mt-3 mt-md-4">

        <?php if (!$entries) { ?>
            <p class="no_entries alert alert-info my-4">
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

    <?php if ($entries && $perpage < $total) { ?>
        <div class="wall_pages" <?php if($max_entries && (count($entries) > $max_entries) && $page==1) {?>style="display:none"<?php } ?>>
            <?php echo html_pagebar($page, $perpage, $total, '#wall'); ?>
        </div>
    <?php } ?>

</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_WALL_ENTRY_DELETED', 'LANG_WALL_ENTRY_DELETE_CONFIRM'); ?>
    <?php if ($show_id) { ?>
        <?php if ($go_reply && !$user->is_logged) { $go_reply = false; } ?>
        icms.wall.show(<?php echo $show_id; ?>, <?php echo $show_reply_id; ?>, <?php echo $go_reply; ?>);
    <?php } ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>
