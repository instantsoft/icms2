<?php if ($groups){ ?>

    <?php
        $index_first = $page * $perpage - $perpage + 1;
        $index = 0;
    ?>

    <div class="groups-list striped-list list-64">

        <?php foreach($groups as $group){ ?>

            <div class="item">

                <?php if ($dataset_name == 'rating') { ?>
                    <div class="position">
                        <?php $position = $index_first + $index; ?>
                        <?php if (in_array($position, range(1, 3))){ ?>
                            <div class="medal-icon-32 medal<?php echo $position; ?>-32" title="<?php echo $position; ?>"></div>
                        <?php } else {  ?>
                            <?php echo $position; ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="icon">
                    <?php
                        echo html_image($group['logo'], 'small', $group['title']);
                    ?>
                </div>

                <div class="title">
                    <a href="<?php echo $this->href_to($group['id']); ?>"><?php html($group['title']); ?></a>
                    <?php if ($group['is_closed']) { ?>
                        <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
                    <?php } ?>
                </div>

                <div class="actions">

                    <?php if ($dataset_name == 'popular') { ?>

                        <?php echo $group['members_count'] ? html_spellcount($group['members_count'], LANG_GROUPS_MEMBERS_SPELLCOUNT) : '&mdash;'; ?>

                    <?php } elseif ($dataset_name == 'rating') { ?>

                        <span class="rate_value rating" title="<?php echo LANG_RATING; ?>"><?php echo $group['rating']; ?></span>

                    <?php } else { ?>

                        <?php echo html_date($group['date_pub']); ?>

                    <?php } ?>

                </div>

            </div>

            <?php $index++; ?>

        <?php } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
    <?php } ?>

<?php } ?>
