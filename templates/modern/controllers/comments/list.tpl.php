<?php // Шаблон списка комментариев и формы добавления //
$this->addTplJSName('jquery-scroll');
$this->addTplJSName('comments');
?>
<?php if ($can_add || $user->is_logged){ ?>
    <div class="d-flex justify-content-between align-items-center mt-3 mt-md-4">
    <?php if ($can_add){ ?>
        <div id="comments_add_link">
            <a href="#reply" class="btn btn-primary ajaxlink" onclick="return icms.comments.add()">
                <?php echo $this->controller->labels->add; ?>
            </a>
        </div>
    <?php } ?>
    <?php if ($user->is_logged){ ?>
        <div class="d-flex">
            <?php if ($is_karma_allowed){ ?>
                <a href="#" id="is_track" data-is_tracking="<?php echo (int)!$is_tracking; ?>" class="btn btn-<?php if($is_tracking){ ?>secondary<?php } else { ?>primary<?php } ?> icms-comments-tracking position-relative mr-2" data-toggle="tooltip" data-placement="top" title="<?php html(!$is_tracking ? $this->controller->labels->track : LANG_COMMENTS_TRACK_STOP); ?>" data-tracking_title="<?php html($this->controller->labels->track); ?>" data-tracking_stop_title="<?php html(LANG_COMMENTS_TRACK_STOP); ?>">
                    <b class="icms-comments-track-icon <?php if($is_tracking){ ?>d-none<?php } ?>">
                        <?php html_svg_icon('solid', 'bell'); ?>
                    </b>
                    <b class="icms-comments-track-icon <?php if(!$is_tracking){ ?>d-none<?php } ?>">
                        <?php html_svg_icon('solid', 'bell-slash'); ?>
                    </b>
                </a>
            <?php } ?>
            <a id="icms-refresh-id" href="#refresh" data-toggle="tooltip" data-placement="right" class="btn btn-secondary refresh_btn d-none" onclick="return icms.comments.refresh()" title="<?php echo $this->controller->labels->refresh; ?>">
                <?php html_svg_icon('solid', 'sync-alt'); ?>
            </a>
        </div>
    <?php } ?>
    </div>
<?php } ?>

<div id="comments_list" class="my-3 my-md-4">

    <?php if (!$comments){ ?>

        <div class="no_comments alert alert-info my-4">
            <?php echo $this->controller->labels->none; ?>
        </div>

        <?php if (!$user->is_logged && !$is_guests_allowed) { ?>
            <div class="login_to_comment alert alert-info my-4">
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
            'is_can_rate'      => $is_can_rate,
            'is_levels'        => true,
            'is_controls'      => true,
            'is_show_target'   => false
        )); ?>

    <?php } ?>

</div>

<?php if ($can_add){ ?>
    <div id="comments_add_form">
        <?php if ($is_karma_allowed || (!$user->is_logged && $is_guests_allowed)){ ?>
            <div class="preview_box alert alert-light border mt-3 d-none"></div>
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
                    <div class="author_data form-row">
                        <div class="name form-group col-md-6">
                            <label>
                                <?php echo LANG_COMMENTS_AUTHOR_NAME; ?>
                            </label>
                            <?php echo html_input('text', 'author_name', $guest_name); ?>
                        </div>
                        <?php if(!empty($this->controller->options['show_author_email'])){ ?>
                        <div class="email form-group col-md-6">
                            <label>
                                <?php echo LANG_COMMENTS_AUTHOR_EMAIL; ?>
                            </label>
                            <?php echo html_input('text', 'author_email', $guest_email); ?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php $this->block('comments_add_form'); ?>
                <?php echo html_wysiwyg('content', '', $editor_params['editor'], $editor_params['options']); ?>
                <div class="buttons row justify-content-between">
                    <div class="col">
                        <?php echo html_button(LANG_SEND, 'submit', 'icms.comments.submit()', ['class' => 'button-add button-update btn-primary']); ?>
                        <?php echo html_button(LANG_CANCEL, 'cancel', 'icms.comments.restoreForm()', ['class'=>'btn-secondary button-cancel']); ?>
                    </div>
                    <div class="col-auto">
                        <button class="button btn button-preview btn-info" name="preview" onclick="icms.comments.preview()" type="button">
                            <?php html_svg_icon('solid', 'eye'); ?>
                            <span class="d-none d-lg-inline-block"><?php echo LANG_PREVIEW; ?></span>
                        </button>
                    </div>
                </div>
            </form>
            <?php $this->block('comments_add_form_after'); ?>
        <?php } else { ?>
            <p class="alert alert-info"><?php printf($this->controller->labels->low_karma, cmsUser::getPermissionValue('comments', 'karma')); ?></p>
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