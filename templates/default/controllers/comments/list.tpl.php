<?php // Шаблон списка комментариев и формы добавления //

$this->addJS('templates/default/js/jquery-scroll.js');
$this->addJS('templates/default/js/comments.js');
$is_guests_allowed =  !empty($this->controller->options['is_guests']);
$is_karma_allowed = $user->is_logged && !cmsUser::isPermittedLimitHigher('comments', 'karma', $user->karma);

?>
<?php if ($rss_link){ ?>
    <div class="content_list_rss_icon">
        <a href="<?php echo $rss_link; ?>">RSS</a>
    </div>
<?php } ?>
<?php if ($user->is_logged){ ?>
    <?php if ($is_karma_allowed){ ?>
        <div class="track">
            <label><input type="checkbox" id="is_track" name="is_track" value="1" <?php if($is_tracking){ ?>checked="checked"<?php } ?> /> <?php echo LANG_COMMENTS_TRACK; ?></label>
        </div>
    <?php } ?>
    <div id="comments_refresh_panel">
        <a href="#refresh" class="refresh_btn" onclick="return icms.comments.refresh()" title="<?php echo LANG_COMMENTS_REFRESH; ?>"></a>
    </div>
<?php } ?>

<div id="comments_list">

    <?php if (!$comments){ ?>

        <div class="no_comments">
            <?php echo LANG_COMMENTS_NONE; ?>
        </div>

        <?php if (!$user->is_logged && !$is_guests_allowed) { ?>
            <div class="login_to_comment">
                <?php
                    $reg_url = href_to('auth', 'register');
                    $log_url = href_to('auth', 'login');
                    printf(LANG_COMMENTS_LOGIN, $log_url, $reg_url);
                ?>
            </div>
        <?php } ?>

    <?php } ?>

    <?php if ($comments){ ?>

        <?php echo $this->renderChild('comment', array('comments'=>$comments, 'target_user_id'=>$target_user_id, 'user'=>$user, 'is_highlight_new'=>$is_highlight_new, 'is_can_rate' => $is_can_rate)); ?>

    <?php } ?>

</div>

<div id="comments_urls" style="display: none"
        data-get-url="<?php echo $this->href_to('get'); ?>"
        data-approve-url="<?php echo $this->href_to('approve'); ?>"
        data-delete-url="<?php echo $this->href_to('delete'); ?>"
        data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
        data-track-url="<?php echo $this->href_to('track'); ?>"
        data-rate-url="<?php echo $this->href_to('rate'); ?>"
></div>

<?php if (($user->is_logged && cmsUser::isAllowed('comments', 'add')) || (!$user->is_logged && $is_guests_allowed)){ ?>
    <div id="comments_add_link">
        <a href="#reply" class="ajaxlink" onclick="return icms.comments.add()"><?php echo LANG_COMMENT_ADD; ?></a>
    </div>

    <div id="comments_add_form">
        <?php if ($is_karma_allowed || $is_guests_allowed){ ?>
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
                        $this->addJS('templates/default/js/jquery-cookie.js');
                        $name = cmsUser::getCookie('comments_guest_name');
                        $email = cmsUser::getCookie('comments_guest_email');
                    ?>
                    <div class="author_data">
                        <div class="name field">
                            <label><?php echo LANG_COMMENTS_AUTHOR_NAME; ?>:</label> <?php echo html_input('text', 'author_name', $name); ?>
                        </div>
                        <div class="email field">
                            <label><?php echo LANG_COMMENTS_AUTHOR_EMAIL; ?>:</label> <?php echo html_input('text', 'author_email', $email); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php echo $user->is_logged ? html_editor('content') : html_textarea('content'); ?>
                <div class="buttons">
                    <?php echo html_button(LANG_PREVIEW, 'preview', 'icms.comments.preview()'); ?>
                    <?php echo html_button(LANG_SEND, 'submit', 'icms.comments.submit()'); ?>
                    <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.comments.restoreForm()', array('class'=>'button-cancel')); ?>
                </div>
                <div class="loading">
                    <?php echo LANG_LOADING; ?>
                </div>
            </form>
        <?php } else { ?>
            <p><?php printf(LANG_COMMENTS_LOW_KARMA, cmsUser::getPermissionValue('comments', 'karma')); ?></p>
        <?php } ?>
    </div>
<?php } ?>

<script type="text/javascript">
    <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_COMMENT_DELETED', 'LANG_COMMENT_DELETE_CONFIRM'); ?>
    <?php if ($is_highlight_new){ ?>icms.comments.showFirstSelected();<?php } ?>
</script>