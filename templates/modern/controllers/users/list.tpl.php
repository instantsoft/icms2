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
        $pos_colors = ['text-muted', 'text-warning','text-info', 'text-secondary'];
    ?>

    <div id="users_profiles_list" class="content_list striped-list mt-3 mt-md-4">

        <?php foreach($profiles as $profile){ ?>

            <div class="item media mb-3 mb-md-4 align-items-center <?php if (!empty($profile['item_css_class'])) { ?> <?php echo implode(' ', $profile['item_css_class']); ?><?php } ?>">

                <?php if ($dataset_name == 'rating') { ?>
                    <?php $position = $index_first + $index; ?>
                    <div class="position icms-svg-icon w-32 mr-2 text-center <?php echo isset($pos_colors[$position]) ? $pos_colors[$position] : $pos_colors[0]; ?>">
                        <?php if (in_array($position, range(1, 3))){ ?>
                            <?php html_svg_icon('solid', 'medal', 32); ?>
                        <?php } else {  ?>
                            <?php echo $position; ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($fields['avatar']) && $fields['avatar']['is_in_list']){ ?>
                    <a href="<?php echo href_to_profile($profile); ?>" class="icms-user-avatar mr-3 <?php if (!empty($profile['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                    <?php if($profile['avatar']){ ?>
                        <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
                    <?php } ?>
                    </a>
                <?php } ?>

                <div class="media-body text-truncate">
                    <?php if (!empty($fields['nickname']) && $fields['nickname']['is_in_list']){ ?>
                        <h5 class="my-0">
                            <a href="<?php echo href_to_profile($profile); ?>">
                                <?php html($profile['nickname']); ?>
                            </a>
                        </h5>
                    <?php } ?>
                    <?php if (!empty($profile['fields'])){ ?>
                    <div class="fields mt-2">
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
                    </div>
                    <?php } ?>
                </div>

                <div class="actions text-muted" <?php if (!empty($profile['notice_title'])) { ?>data-notice_title="<?php echo implode(', ', $profile['notice_title']); ?>"<?php } ?>>
                    <?php if ($dataset_name == 'popular') { ?>

                        <?php echo $profile['friends_count'] ? html_spellcount($profile['friends_count'], LANG_USERS_FRIENDS_SPELLCOUNT) : '&mdash;'; ?>

                    <?php } elseif ($dataset_name == 'rating') { ?>

                        <span class="rate_value karma <?php echo html_signed_class($profile['karma']); ?>" title="<?php echo LANG_KARMA; ?>"><?php echo html_signed_num($profile['karma']); ?></span> /
                        <span class="rate_value rating" title="<?php echo LANG_RATING; ?>"><?php echo $profile['rating']; ?></span>

                    <?php } else { ?>

                        <?php if (!$profile['is_online']){ ?>
                            <small><?php echo string_date_age_max($profile['date_log'], true); ?></small>
                        <?php } else { ?>
                            <small class="text-success is_online"><?php echo LANG_ONLINE; ?></small>
                        <?php } ?>

                    <?php } ?>

                </div>
                <?php if (!empty($profile['actions'])){ ?>
                <div class="dropdown ml-2">
                    <button class="btn btn-dylan" type="button" data-toggle="dropdown">
                        <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php foreach($profile['actions'] as $action){ ?>
                            <a class="dropdown-item <?php echo $action['class']; ?>" href="<?php echo $action['href']; ?>" title="<?php html($action['title']); ?>">
                                <?php echo $action['title']; ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

            </div>

            <?php $index++; ?>

        <?php } ?>

    </div>

    <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>

<?php } else { ?>
    <div class="alert alert-info mt-4" role="alert">
        <?php echo sprintf(LANG_TARGET_LIST_EMPTY, LANG_USERS_GEN); ?>
    </div>
<?php } ?>