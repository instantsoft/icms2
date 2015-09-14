<?php
    if( $this->controller->options['is_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix' => 'profiles',
            'page_url' => $page_url,
            'fields' => $fields,
            'filters' => $filters,
        ));
    }
?>

<?php if ($profiles){ ?>

    <?php
        $index_first = $page * $perpage - $perpage + 1;
        $index = 0;
    ?>

    <div id="users_profiles_list" class="striped-list list-32">

        <?php foreach($profiles as $profile){ ?>

            <div class="item">

                <?php if ($dataset_name == 'rating') { ?>
                    <div class="position">
                        <?php $position = $index_first + $index; ?>
                        <?php if (in_array($position, range(1, 3))){ ?>
                            <div class="medal-icon-16 medal<?php echo $position; ?>-16" title="<?php echo $position; ?>"></div>
                        <?php } else {  ?>
                            <?php echo $position; ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="icon">
					<a href="<?php echo $this->href_to($profile['id']); ?>"><?php echo html_avatar_image($profile['avatar'], 'micro', $profile['nickname']); ?></a>
                </div>

                <div class="title">
                    <a href="<?php echo $this->href_to($profile['id']); ?>"><?php html($profile['nickname']); ?></a>
                </div>

                <div class="actions">

                    <?php if ($dataset_name == 'popular') { ?>

                        <?php echo $profile['friends_count'] ? html_spellcount($profile['friends_count'], LANG_USERS_FRIENDS_SPELLCOUNT) : '&mdash;'; ?>

                    <?php } elseif ($dataset_name == 'rating') { ?>

                        <span class="rate_value karma <?php echo html_signed_class($profile['karma']); ?>" title="<?php echo LANG_KARMA; ?>"><?php echo html_signed_num($profile['karma']); ?></span> /
                        <span class="rate_value rating" title="<?php echo LANG_RATING; ?>"><?php echo $profile['rating']; ?></span>

                    <?php } else { ?>

                        <?php if (!$profile['is_online']){ ?>
                            <?php echo string_date_age_max($profile['date_log'], true); ?>
                        <?php } else { ?>
                            <span class="is_online"><?php echo LANG_ONLINE; ?></span>
                        <?php } ?>

                    <?php } ?>

                </div>

            </div>

            <?php $index++; ?>

        <?php } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } ?>
