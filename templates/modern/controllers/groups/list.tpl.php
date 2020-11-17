<?php
    if(!empty($this->controller->options['is_filter'])) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix' => 'groups',
            'page_url'   => $page_url,
            'fields'     => $fields,
            'filters'    => $filters
        ));
    }
?>

<?php if ($groups){ ?>

    <?php
        $index_first = $page * $perpage - $perpage + 1;
        $index = 0;
        $pos_colors = ['text-muted', 'text-warning','text-info', 'text-secondary'];
    ?>

    <div class="groups-list content_list striped-list mt-3 mt-md-4">

        <?php foreach($groups as $group){ ?>

            <div class="item media mb-3 align-items-center">

                <?php if ($dataset_name == 'rating') { ?>
                    <div class="position">
                        <?php $position = $index_first + $index; ?>
                        <div class="position icms-svg-icon w-32 mr-2 text-center <?php echo isset($pos_colors[$position]) ? $pos_colors[$position] : $pos_colors[0]; ?>">
                            <?php if (in_array($position, range(1, 3))){ ?>
                                <?php html_svg_icon('solid', 'medal', 32); ?>
                            <?php } else {  ?>
                                <?php echo $position; ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($fields['logo']) && $fields['logo']['is_in_list'] && $group['logo']){ ?>
                    <a class="icms-user-avatar d-flex mr-3" href="<?php echo href_to('groups', $group['slug']); ?>">
                        <?php echo html_image($group['logo'], $fields['logo']['handler']->getOption('size_teaser'), $group['title']); ?>
                    </a>
                <?php } ?>

                <div class="media-body">
                    <?php if (!empty($fields['title']) && $fields['title']['is_in_list']){ ?>
                        <h5 class="my-0">
                            <a href="<?php echo href_to('groups', $group['slug']); ?>"><?php html($group['title']); ?></a>
                            <?php if ($group['is_closed']) { ?>
                                <span class="is_closed text-muted ml-2" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>" data-toggle="tooltip" data-placement="top">
                                    <?php html_svg_icon('solid', 'lock'); ?>
                                </span>
                            <?php } ?>
                        </h5>
                    <?php } ?>
                    <?php if (!empty($group['fields'])) { ?>
                        <div class="fields mt-2">
                            <?php foreach($group['fields'] as $field){ ?>
                                <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                                    <?php if ($field['label_pos'] != 'none'){ ?>
                                        <div class="title_<?php echo $field['label_pos']; ?>">
                                            <?php echo $field['title'] . ($field['label_pos'] == 'left' ? ': ' : ''); ?>
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

                <div class="ml-3 actions text-muted d-none d-lg-block">

                    <?php if (!$dataset_name || $dataset_name == 'popular') { ?>

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

    <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>

<?php }
