<?php if ($items){ ?>

    <?php
        $last_date = '';
        $today_date = date('j F Y');
        $yesterday_date = date('j F Y', time()-3600*24);
        $is_can_delete = cmsUser::isAllowed('activity', 'delete');
    ?>

    <div class="activity-list striped-list list-32">
        <?php foreach($items as $item) { ?>

            <?php $item_date = date('j F Y', strtotime($item['date_pub'])); ?>
            <?php if ($item_date != $last_date){ ?>

                <?php
                    switch($item_date){
                        case $today_date: $date = LANG_TODAY; break;
                        case $yesterday_date: $date = LANG_YESTERDAY; break;
                        default: $date = lang_date($item_date);
                    }
                ?>

                <h3>
                    <svg aria-hidden="true" class="octicon" height="18" version="1.1" viewBox="0 0 14 16" width="14"><path fill-rule="evenodd" d="M10.86 7c-.45-1.72-2-3-3.86-3-1.86 0-3.41 1.28-3.86 3H0v2h3.14c.45 1.72 2 3 3.86 3 1.86 0 3.41-1.28 3.86-3H14V7h-3.14zM7 10.2c-1.22 0-2.2-.98-2.2-2.2 0-1.22.98-2.2 2.2-2.2 1.22 0 2.2.98 2.2 2.2 0 1.22-.98 2.2-2.2 2.2z"></path></svg>
                    <?php echo $date; ?>
                </h3>
                <?php $last_date = $item_date; ?>

            <?php } ?>

            <?php $url = href_to_profile($item['user']); ?>

            <div class="item">
                <?php if ($is_can_delete) { ?>
                    <div class="actions">
                        <a class="delete" href="<?php echo $this->href_to('delete', $item['id'], ['csrf_token' => cmsForm::getCSRFToken()]); ?>" title="<?php html(LANG_DELETE); ?>"></a>
                    </div>
                <?php } ?>
                <div class="icon">
                    <a href="<?php echo $url; ?>" <?php if (!empty($item['user']['is_online'])){ ?>class="peer_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="peer_no_online"<?php } ?>>
                        <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                    </a>
                </div>
                <div class="title-multiline">
                    <a class="author" href="<?php echo $url; ?>"><?php html($item['user']['nickname']); ?></a>
                    <?php echo $item['description']; ?>
                    <?php if ($item['is_private']) { ?>
                        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                    <?php } ?>
                    <div class="details">
                        <span class="date<?php if(!empty($item['is_new'])){ ?> highlight_new<?php } ?>"><?php echo $item['date_diff']; ?></span>
                        <?php if (!empty($item['reply_url']) && cmsUser::isLogged()) { ?>
                            <span class="reply">
                                <a href="<?php echo $item['reply_url']; ?>"><?php echo LANG_REPLY; ?></a>
                            </span>
                        <?php } ?>
                    </div>
                    <?php if (!empty($item['images'])) { ?>
                        <div class="images">
                            <?php foreach($item['images'] as $image){ ?>
                                <div class="image">
                                    <a href="<?php echo $image['url']; ?>">
                                        <img src="<?php echo $image['src']; ?>" alt="<?php html(!empty($image['title']) ? $image['title'] : $item['subject_title']); ?>">
                                    </a>
                                </div>
                            <?php } ?>
                            <?php if($item['images_count'] > 5){ ?>
                                <div class="image more">
                                    <a href="<?php echo $item['subject_url']; ?>">+<span><?php echo ($item['images_count']-4); ?></span></a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php } ?>
    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { echo LANG_LIST_EMPTY; } ?>
