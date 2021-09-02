<?php
$this->addTplJSName([
    'jquery-scroll',
    'comments']
    );
?>
<?php if ($items){ ?>
    <div id="comments_list" class="striped-list mt-3 mt-md-4">
        <?php
            echo $this->renderChild('comment', array(
                'comments'         => $items,
                'user'             => $user,
                'target_user_id'   => 0,
                'is_highlight_new' => false,
                'is_levels'        => false,
                'is_controls'      => false,
                'is_moderator'     => $is_moderator,
                'is_show_target'   => true
            ));
        ?>
    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
    <?php } ?>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SEND', 'LANG_SAVE', 'LANG_COMMENT_DELETED', 'LANG_COMMENT_DELETE_CONFIRM', 'LANG_MODERATION_REFUSE_REASON'); ?>
    icms.comments.is_moderation_list = true;
    icms.comments.init({
        get:'<?php echo $this->href_to('get'); ?>',
        approve:'<?php echo $this->href_to('approve'); ?>',
        delete:'<?php echo $this->href_to('delete'); ?>',
        refresh:'<?php echo $this->href_to('refresh'); ?>',
        track:'<?php echo $this->href_to('track'); ?>',
        rate:'<?php echo $this->href_to('rate'); ?>'
        });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<?php }
