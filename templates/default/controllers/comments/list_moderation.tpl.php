<?php
$this->addTplJSName([
    'jquery-scroll',
    'comments']
    );
?>
<?php if ($items){ ?>
    <div id="comments_list" class="striped-list">
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
    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
    <?php } ?>
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
<?php } else { echo LANG_LIST_EMPTY; }
