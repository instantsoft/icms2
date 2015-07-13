<?php if ($items){ ?>

    <div id="comments_list" class="striped-list">

        <?php
            echo $this->renderChild('comment', array(
                'comments'=>$items,
                'user'=>$user,
                'is_highlight_new'=>false,
                'is_levels'=>false,
                'is_controls'=>false,
                'is_show_target'=>true
            ));
        ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { echo LANG_LIST_EMPTY; } ?>
