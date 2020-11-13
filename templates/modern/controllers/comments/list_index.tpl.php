<?php if ($items){ ?>
    <div id="comments_list" class="striped-list mt-3 mt-md-4">
        <?php
            echo $this->renderChild('comment', array(
                'comments'         => $items,
                'user'             => $user,
                'target_user_id'   => $target_user_id,
                'is_highlight_new' => false,
                'is_levels'        => false,
                'is_controls'      => false,
                'is_show_target'   => true
            ));
        ?>
    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { ?>

    <p class="alert alert-info mt-3 mt-md-4" role="alert">
        <?php echo LANG_LIST_EMPTY; ?>
    </p>

<?php } ?>