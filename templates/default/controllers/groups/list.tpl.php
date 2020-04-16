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

                <?php if (!empty($fields['logo']) && $fields['logo']['is_in_list'] && $group['logo']){ ?>
                    <div class="icon">
                        <a href="<?php echo href_to('groups', $group['slug']); ?>">
                            <?php echo html_image($group['logo'], $fields['logo']['handler']->getOption('size_teaser'), $group['title']); ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="title <?php if (!empty($group['fields'])) { ?>fields_available<?php } ?>">
                    <?php if (!empty($fields['title']) && $fields['title']['is_in_list']){ ?>
                        <a href="<?php echo href_to('groups', $group['slug']); ?>"><?php html($group['title']); ?></a>
                        <?php if ($group['is_closed']) { ?>
                            <span class="is_closed" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>"></span>
                        <?php } ?>
                    <?php } ?>
                    <?php if (!empty($group['fields'])) { ?>
                        <div class="fields">
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

                <div class="actions">

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

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php }
