<?php
/**
 * Template Name: LANG_CP_LISTVIEW_STYLE_TABLE
 * Template Type: content
 */
    if( $ctype['options']['list_show_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix'   => $ctype['name'],
            'page_url'     => $page_url,
            'fields'       => $fields,
            'props_fields' => $props_fields,
            'props'        => $props,
            'filters'      => $filters,
            'ext_hidden_params' => $ext_hidden_params,
            'is_expanded'  => $ctype['options']['list_expand_filter']
        ));
    }
?>

<?php if ($items){ ?>

    <div class="content_list table <?php echo $ctype['name']; ?>_list">

        <table>
            <thead>
                <tr>
                    <?php if (isset($fields['photo']) && $fields['photo']['is_in_list']){ ?>
                        <th>&nbsp;</th>
                    <?php } ?>
                    <?php if ($ctype['is_rating']){ ?>
                        <th><?php echo LANG_RATING; ?></th>
                    <?php } ?>

                    <?php foreach($fields as $name => $field){ ?>
                        <?php if ($field['is_system'] || !$field['is_in_list']) { unset($fields[$name]); continue; } ?>
                        <?php if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { unset($fields[$name]); continue; } ?>
                        <?php
                            if (!isset($field['options']['label_in_list'])) {
                                $label_pos = 'none';
                            } else {
                                $label_pos = $field['options']['label_in_list'];
                            }
                        ?>
                        <th>
                            <?php echo $label_pos!='none' ? $field['title'] : '&nbsp'; ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item){ ?>
                    <tr<?php if (!empty($item['is_vip'])){ ?> class="is_vip"<?php } ?>>
                        <?php if (isset($fields['photo']) && $fields['photo']['is_in_list']){ ?>
                            <td class="photo">
                                <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                    <?php if (!empty($item['photo'])){ ?>
                                        <?php echo html_image($item['photo'], $fields['photo']['options']['size_teaser'], $item['title']); ?>
                                        <?php unset($item['photo']); ?>
                                    <?php } ?>
                                </a>
                            </td>
                        <?php } ?>
                        <?php if (!empty($item['rating_widget'])){ ?>
                            <td class="rating">
                                <?php echo $item['rating_widget']; ?>
                            </td>
                        <?php } ?>
                        <?php foreach($fields as $field){ ?>
                            <?php if (!isset($item[$field['name']]) || (!$item[$field['name']] && $item[$field['name']] !== '0')) {
                                echo '<td>&nbsp;</td>'; continue;
                            } ?>
                            <td class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                                <?php if ($field['name'] == 'title' && $ctype['options']['item_on']){ ?>
                                    <h2>
                                    <?php if ($item['parent_id']){ ?>
                                        <a class="parent_title" href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                                        &rarr;
                                    <?php } ?>
                                    <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>"><?php html($item[$field['name']]); ?></a>
                                    </h2>
                                <?php } else { ?>
                                    <?php echo $field['handler']->setItem($item)->parseTeaser($item[$field['name']]); ?>
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, array_merge($filters, $ext_hidden_params)); ?>
    <?php } ?>

<?php  } else {

    if(!empty($ctype['labels']['many'])){
        echo sprintf(LANG_TARGET_LIST_EMPTY, $ctype['labels']['many']);
    } else {
        echo LANG_LIST_EMPTY;
    }

}
