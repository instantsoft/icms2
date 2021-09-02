<?php // Шаблон списка комментариев и формы добавления //
$this->addTplJSName('jquery-scroll');
$this->addTplJSName('comments');
?>
<?php if ($rss_link){ ?>
    <div class="content_list_rss_icon">
        <a href="<?php echo $rss_link; ?>">RSS</a>
    </div>
<?php } ?>
<?php if ($user->is_logged){ ?>
    <?php if ($is_karma_allowed){ ?>
        <div class="track">
            <label><input type="checkbox" id="is_track" name="is_track" value="1" <?php if($is_tracking){ ?>checked="checked"<?php } ?> /> <?php echo $this->controller->labels->track; ?></label>
        </div>
    <?php } ?>
    <div id="comments_refresh_panel">
        <a href="#refresh" class="refresh_btn" onclick="return icms.comments.refresh()" title="<?php echo $this->controller->labels->refresh; ?>"></a>
    </div>
<?php } ?>
<?php if ($can_add){ ?>
    <div id="comments_add_link">
        <a href="#reply" class="ajaxlink" onclick="return icms.comments.add()"><?php echo $this->controller->labels->add; ?></a>
    </div>
<?php } ?>
<div id="comments_list">

    <?php if (!$comments){ ?>

        <div class="no_comments">
            <?php echo $this->controller->labels->none; ?>
        </div>

        <?php if (!$user->is_logged && !$is_guests_allowed) { ?>
            <div class="login_to_comment">
                <?php
                    $reg_url = href_to('auth', 'register');
                    $log_url = href_to('auth', 'login');
                    printf($this->controller->labels->login, $log_url, $reg_url);
                ?>
            </div>
        <?php } ?>

    <?php } ?>

    <?php if ($comments){ ?>

        <?php echo $this->renderChild($this->controller->comment_template, array(
            'comments'         => $comments,
            'target_user_id'   => $target_user_id,
            'user'             => $user,
            'is_highlight_new' => $is_highlight_new,
            'is_can_rate'      => $is_can_rate
        )); ?>

    <?php } ?>

</div>
<?php // #comments_urls deprecated
?>
<div id="comments_urls" style="display: none"
        data-get-url="<?php echo $this->href_to('get'); ?>"
        data-approve-url="<?php echo $this->href_to('approve'); ?>"
        data-delete-url="<?php echo $this->href_to('delete'); ?>"
        data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
        data-track-url="<?php echo $this->href_to('track'); ?>"
        data-rate-url="<?php echo $this->href_to('rate'); ?>"
></div>

<?php if ($can_add){ ?>
    <div id="comments_add_form">
        <?php if ($is_karma_allowed || (!$user->is_logged && $is_guests_allowed)){ ?>
            <div class="preview_box"></div>
            <form action="<?php echo $this->href_to('submit'); ?>" method="post">
                <?php echo html_csrf_token($csrf_token_seed); ?>
                <?php echo html_input('hidden', 'action', 'add'); ?>
                <?php echo html_input('hidden', 'id', 0); ?>
                <?php echo html_input('hidden', 'parent_id', 0); ?>
                <?php echo html_input('hidden', 'tc', $target_controller); ?>
                <?php echo html_input('hidden', 'ts', $target_subject); ?>
                <?php echo html_input('hidden', 'ti', $target_id); ?>
                <?php echo html_input('hidden', 'tud', $target_user_id); ?>
                <?php echo html_input('hidden', 'timestamp', time()); ?>
                <?php if (!$user->is_logged) { ?>
                    <?php
                        $this->addTplJSName('jquery-cookie');
                    ?>
                    <div class="author_data">
                        <div class="name field">
                            <label><?php echo LANG_COMMENTS_AUTHOR_NAME; ?>:</label> <?php echo html_input('text', 'author_name', $guest_name); ?>
                        </div>
                        <?php if(!empty($this->controller->options['show_author_email'])){ ?>
                        <div class="email field">
                            <label><?php echo LANG_COMMENTS_AUTHOR_EMAIL; ?>:</label> <?php echo html_input('text', 'author_email', $guest_email); ?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php $this->block('comments_add_form'); ?>
                <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
                <div class="buttons">
                    <?php echo html_button(LANG_PREVIEW, 'preview', 'icms.comments.preview()', array('class'=>'button-preview')); ?>
                    <?php echo html_button(LANG_SEND, 'submit', 'icms.comments.submit()'); ?>
                    <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.comments.restoreForm()', array('class'=>'button-cancel')); ?>
                </div>
                <div class="loading">
                    <?php echo LANG_LOADING; ?>
                </div>
            </form>
            <?php $this->block('comments_add_form_after'); ?>
        <?php } else { ?>
            <p><?php printf($this->controller->labels->low_karma, cmsUser::getPermissionValue('comments', 'karma')); ?></p>
        <?php } ?>
    </div>
<?php } ?>
<?php $this->block('comments_list_after'); ?>

<?php ob_start(); ?>
    <script>
        <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_COMMENT_DELETED', 'LANG_COMMENT_DELETE_CONFIRM', 'LANG_MODERATION_REFUSE_REASON'); ?>
        <?php if ($is_highlight_new){ ?>icms.comments.showFirstSelected();<?php } ?>
        icms.comments.init({
            get:'<?php echo $this->href_to('get'); ?>',
            approve:'<?php echo $this->href_to('approve'); ?>',
            delete:'<?php echo $this->href_to('delete'); ?>',
            refresh:'<?php echo $this->href_to('refresh'); ?>',
            track:'<?php echo $this->href_to('track'); ?>',
            rate:'<?php echo $this->href_to('rate'); ?>'
            },{
            tc:'<?php echo $target_controller; ?>',
            ts:'<?php echo $target_subject; ?>',
            ti:'<?php echo $target_id; ?>',
            tud:'<?php echo $target_user_id; ?>',
            timestamp:'<?php echo time(); ?>'
        });
    </script>
<?php $this->addBottom(ob_get_clean()); ?>