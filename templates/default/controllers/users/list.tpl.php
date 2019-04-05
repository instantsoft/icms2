<?php
    if( $this->controller->options['is_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix' => 'users',
            'page_url'   => $page_url,
            'fields'     => $fields,
            'filters'    => $filters
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

            <div class="item<?php if (!empty($profile['item_css_class'])) { ?> <?php echo implode(' ', $profile['item_css_class']); ?><?php } ?>">

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

                <?php if (!empty($fields['avatar']) && $fields['avatar']['is_in_list']){ ?>
                    <div class="icon">
                        <a href="<?php echo href_to_profile($profile); ?>" <?php if (!empty($profile['is_online'])){ ?>class="peer_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="peer_no_online"<?php } ?>>
                            <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname'], $profile['is_deleted']); ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="title">
                    <?php if (!empty($fields['nickname']) && $fields['nickname']['is_in_list']){ ?>
                        <a href="<?php echo href_to_profile($profile); ?>">
                            <?php html($profile['nickname']); ?>
                        </a>
                    <?php } ?>
                    <div class="fields">
                        <?php if (!empty($profile['fields'])){ ?>
                            <?php foreach($profile['fields'] as $field){ ?>
                                <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                                    <?php if ($field['label_pos'] != 'none'){ ?>
                                        <div class="title_<?php echo $field['label_pos']; ?>">
                                            <?php echo $field['title'] . ($field['label_pos']=='left' ? ': ' : ''); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="value">
                                        <?php echo $field['html']; ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="actions" <?php if (!empty($profile['notice_title'])) { ?>data-notice_title="<?php echo implode(', ', $profile['notice_title']); ?>"<?php } ?>>

                    <?php if (!empty($profile['actions'])){ ?>
                        <div class="list_actions_menu controller_actions_menu dropdown_menu">
                            <input tabindex="-1" type="checkbox" id="menu_label_<?php echo $profile['id']; ?>">
                            <label for="menu_label_<?php echo $profile['id']; ?>" class="group_menu_title"></label>
                            <ul class="list_actions menu">
                                <?php foreach($profile['actions'] as $action){ ?>
                                    <li>
                                        <a class="<?php echo $action['class']; ?>" href="<?php echo $action['href']; ?>" title="<?php html($action['title']); ?>">
                                            <?php echo $action['title']; ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>

                    <?php if ($dataset_name == 'popular') { ?>

                        <?php echo $profile['friends_count'] ? html_spellcount($profile['friends_count'], LANG_USERS_FRIENDS_SPELLCOUNT) : '&mdash;'; ?>

                    <?php } elseif ($dataset_name == 'rating') { ?>

                        <span class="rate_value karma <?php echo html_signed_class($profile['karma']); ?>" title="<?php echo LANG_KARMA; ?>"><?php echo html_signed_num($profile['karma']); ?></span> /
                        <span class="rate_value rating" title="<?php echo LANG_RATING; ?>"><?php echo $profile['rating']; ?></span>

                    <?php } else { ?>

                        <?php if (!$profile['is_online']){ ?>
                            <span><?php echo string_date_age_max($profile['date_log'], true); ?></span>
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

<?php } else { echo LANG_LIST_EMPTY; }
