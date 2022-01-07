<?php
/**
 * Template Name: LANG_CP_LISTVIEW_STYLE_TILES
 * Template Type: content
 * Template Options: {"cols": "2"}
 */
if($ctype['options']['list_show_filter']) {
    $this->renderAsset('ui/filter-panel', [
        'css_prefix'   => $ctype['name'],
        'page_url'     => $page_url,
        'fields'       => $fields,
        'props_fields' => $props_fields,
        'props'        => $props,
        'filters'      => $filters,
        'ext_hidden_params' => $ext_hidden_params,
        'is_expanded'  => $ctype['options']['list_expand_filter']
    ]);
}
?>
<?php if (!$items){ ?>
    <p class="alert alert-info mt-4 alert-list-empty">
        <?php if(!empty($ctype['labels']['many'])){ ?>
            <?php echo sprintf(LANG_TARGET_LIST_EMPTY, $ctype['labels']['many']); ?>
        <?php } else { ?>
            <?php echo LANG_LIST_EMPTY; ?>
        <?php } ?>
    </p>
<?php return; } ?>

<div class="content_list tiled <?php echo $ctype['name']; ?>_list mb-n4 row">

    <?php foreach($items as $item){ ?>

        <div class="tile <?php echo $ctype['name']; ?>_list_item col-lg-<?php echo 12/(!empty($list_opt['cols']) ? $list_opt['cols'] : 2); ?> mb-3 mb-md-4">
            <div class="icms-content-fields d-flex flex-column h-100">
            <?php foreach($item['fields'] as $field){ ?>

                <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_style']; ?>">

                    <?php if ($field['label_pos'] !== 'none'){ ?>
                        <div class="title_<?php echo $field['label_pos']; ?>">
                            <?php echo string_replace_svg_icons($field['title']) . ($field['label_pos']==='left' ? ': ' : ''); ?>
                        </div>
                    <?php } ?>

                    <?php if ($field['name'] === 'title' && $ctype['options']['item_on']){ ?>
                        <h3 class="h4 m-0">
                        <?php if (!empty($this->menus['list_actions_menu'])){ ?>
                            <div class="dropdown ml-2 float-right">
                                <button class="btn" type="button" data-toggle="dropdown">
                                    <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <?php foreach($this->menus['list_actions_menu'] as $menu){ ?>
                                        <a class="dropdown-item <?php echo isset($menu['options']['class']) ? $menu['options']['class'] : ''; ?>" href="<?php echo string_replace_keys_values($menu['url'], $item); ?>" title="<?php html($menu['title']); ?>">
                                            <?php echo $menu['title']; ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($item['parent_id']){ ?>
                            <a class="parent_title" href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                            &rarr;
                        <?php } ?>

                        <?php if (!empty($item['is_private_item'])) { ?>
                            <?php html($item[$field['name']]); ?>
                            <span class="is_private text-secondary" title="<?php html($item['private_item_hint']); ?>">
                                <?php html_svg_icon('solid', 'lock'); ?>
                            </span>
                        <?php } else { ?>
                            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                <?php html($item[$field['name']]); ?>
                            </a>
                            <?php if ($item['is_private']) { ?>
                                <span class="is_private text-secondary" title="<?php echo LANG_PRIVACY_HINT; ?>">
                                    <?php html_svg_icon('solid', 'lock'); ?>
                                </span>
                            <?php } ?>
                        <?php } ?>
                        </h3>
                    <?php } else { ?>
                        <div class="value">
                            <?php echo $field['html']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($item['show_tags'])){ ?>
                <div class="tags_bar mb-2">
                    <?php echo html_tags_bar($item['tags'], 'content-'.$ctype['name'], 'btn btn-outline-secondary btn-sm icms-btn-tag', ''); ?>
                </div>
            <?php } ?>

            <?php if (!empty($item['info_bar'])){ ?>
                <div class="info_bar p-0 bg-transparent border-0 mt-auto">
                    <?php foreach($item['info_bar'] as $bar){ ?>
                        <div class="bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                            <?php if (!empty($bar['icon'])){ ?>
                                <?php html_svg_icon('solid', $bar['icon']); ?>
                            <?php } ?>
                            <?php if (!empty($bar['href'])){ ?>
                                <a class="stretched-link" href="<?php echo $bar['href']; ?>">
                                    <?php echo $bar['html']; ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $bar['html']; ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            </div>
        </div>

    <?php } ?>

</div>
<?php echo html_pagebar($page, $perpage, $total, $page_url, $filter_query); ?>