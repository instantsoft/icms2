<?php

$user = cmsUser::getInstance();

$is_tags = $ctype['is_tags'] && !empty($ctype['options']['is_tags_in_item']) && $item['tags'];

$show_bar = $is_tags || $item['parent_id'] ||
            $fields['date_pub']['is_in_item'] ||
            $fields['user']['is_in_item'] ||
            !empty($ctype['options']['hits_on']);

?>

<?php if ($fields['title']['is_in_item']){ ?>
    <h1>
        <?php if ($fields['user']['is_in_item'] && !empty($item['folder_title'])){ ?>
            <a href="<?php echo href_to('users', $item['user']['id'], array('content', $ctype['name'], $item['folder_id'])); ?>"><?php echo $item['folder_title']; ?></a>&nbsp;&rarr;&nbsp;
        <?php } ?>
        <?php html($item['title']); ?>
        <?php if ($item['is_private']) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
        <?php } ?>
    </h1>
    <?php if ($show_bar){ ?>
        <h2 class="parent_title">
            <?php if ($fields['user']['is_in_item']){ ?>
                <span class="album_user">
                    <a href="<?php echo href_to('users', $item['user']['id']); ?>">
                        <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                    </a>
                </span>
                <?php echo $fields['user']['html']; ?>
            <?php } ?>
            <?php if ($fields['date_pub']['is_in_item']){ ?>
                <span class="album_date" title="<?php html( $fields['date_pub']['title'] ); ?>">
                    <?php if (!$item['is_pub']){ ?>
                        <span class="bi_not_pub">
                            <?php echo LANG_CONTENT_NOT_IS_PUB; ?>
                        </span>
                    <?php } else { ?>
                        <?php echo $fields['date_pub']['html']; ?>
                    <?php } ?>
                </span>
            <?php } ?>
            <?php if (!empty($ctype['options']['hits_on']) && $item['hits_count']){ ?>
                <span class="album_hits">
                    <?php echo html_spellcount($item['hits_count'], LANG_HITS_SPELL); ?>
                </span>
            <?php } ?>
            <?php if ($item['parent_id']){ ?>
                <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
            <?php } ?>
            <?php if ($is_tags){ ?>
                <span class="tags_bar"><?php echo html_tags_bar($item['tags']); ?></span>
            <?php } ?>
        </h2>
    <?php } ?>
    <?php unset($fields['title']); ?>
<?php } ?>

<div class="photo_filter">
    <form action="<?php echo $item['base_url']; ?>" method="get">
    <span title="<?php echo LANG_SORTING; ?>" class="box_menu <?php echo !isset($item['filter_selected']['ordering']) ?'': 'box_menu_select'; ?>">
        <?php echo $item['filter_panel']['ordering'][$item['filter_values']['ordering']]; ?>
    </span>
    <div class="box_menu_dd">
        <?php foreach($item['filter_panel']['ordering'] as $value => $name){ ?>
            <?php $url_params = $item['url_params']; $url_params['ordering'] = $value; ?>
            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html?'.http_build_query($url_params)); ?>">
                <?php echo $name; ?>
                <?php if($item['filter_values']['ordering'] == $value){ ?>
                    <input type="hidden" name="ordering" value="<?php echo $value; ?>">
                    <i class="check">&larr;</i>
                <?php } ?>
            </a>
        <?php } ?>
    </div>
    <?php if($item['filter_panel']['types']){ ?>
        <span class="box_menu <?php echo !isset($item['filter_selected']['types']) ?'': 'box_menu_select'; ?>">
            <?php echo $item['filter_panel']['types'][$item['filter_values']['types']]; ?>
        </span>
        <div class="box_menu_dd">
            <?php foreach($item['filter_panel']['types'] as $value => $name){ ?>
                <?php $url_params = $item['url_params']; $url_params['types'] = $value; ?>
                <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html?'.http_build_query($url_params)); ?>">
                    <?php echo $name; ?>
                    <?php if($item['filter_values']['types'] == $value){ ?>
                        <input type="hidden" name="types" value="<?php echo $value; ?>">
                        <i class="check">&larr;</i>
                    <?php } ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
    <span class="box_menu <?php echo !isset($item['filter_selected']['orientation']) ?'': 'box_menu_select'; ?>">
        <?php echo $item['filter_panel']['orientation'][$item['filter_values']['orientation']]; ?>
    </span>
    <div class="box_menu_dd">
        <?php foreach($item['filter_panel']['orientation'] as $value => $name){ ?>
            <?php $url_params = $item['url_params']; $url_params['orientation'] = $value; ?>
            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html?'.http_build_query($url_params)); ?>">
                <?php echo $name; ?>
                <?php if($item['filter_values']['orientation'] == $value){ ?>
                    <input type="hidden" name="orientation" value="<?php echo $value; ?>">
                    <i class="check">&larr;</i>
                <?php } ?>
            </a>
        <?php } ?>
    </div>

    <?php if($item['filter_values']['width'] || $item['filter_values']['height']){ ?>
        <span class="box_menu box_menu_select"><?php echo LANG_PHOTOS_MORE_THAN; ?> <?php html($item['filter_values']['width']); ?> x <?php html($item['filter_values']['height']); ?></span>
    <?php } else { ?>
        <span class="box_menu"><?php echo LANG_PHOTOS_SIZE; ?></span>
    <?php } ?>

    <div class="box_menu_dd">
        <div class="size_search_params">
            <fieldset>
                <legend><?php echo LANG_PHOTOS_MORE_THAN; ?></legend>
                <div class="field">
                    <label for="birth_date"><?php echo LANG_PHOTOS_SIZE_W; ?></label>
                    <input type="text" name="width" value="<?php html($item['filter_values']['width']); ?>" placeholder="px" class="input">
                </div>
                <div class="field">
                    <label for="birth_date"><?php echo LANG_PHOTOS_SIZE_H; ?></label>
                    <input type="text" name="height" value="<?php html($item['filter_values']['height']); ?>" placeholder="px" class="input">
                </div>
            </fieldset>
            <div class="buttons">
                <input type="submit" class="button" value="<?php echo LANG_FIND; ?>">
            </div>
        </div>
    </div>

    <?php if($item['filter_selected']) { ?>
        <a title="<?php echo LANG_PHOTOS_CLEAR_FILTER; ?>" class="box_menu clear_filter" href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">x</a>
    <?php } ?>

    </form>
</div>

<div class="content_item <?php echo $ctype['name']; ?>_item">

    <?php foreach($fields as $name=>$field){ ?>

        <?php if (!$field['is_in_item'] || $field['is_system']) { continue; } ?>
        <?php if ((empty($item[$field['name']]) || empty($field['html'])) && $item[$field['name']] !== '0') { continue; } ?>
        <?php if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; } ?>

        <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field" <?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
            <?php if ($field['options']['label_in_item'] != 'none'){ ?>
                <div class="title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
            <?php } ?>
            <div class="value"><?php echo $field['html']; ?></div>
        </div>

    <?php } ?>

    <?php if ($props && array_filter((array)$props_values)) { ?>
        <?php
            $props_fields = $this->controller->getPropsFields($props);
            $props_fieldsets = cmsForm::mapFieldsToFieldsets($props);
        ?>
        <div class="content_item_props <?php echo $ctype['name']; ?>_item_props">
            <table>
                <tbody>
                    <?php foreach($props_fieldsets as $fieldset){ ?>
                        <?php if ($fieldset['title']){ ?>
                            <tr>
                                <td class="heading" colspan="2"><?php html($fieldset['title']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($fieldset['fields']){ ?>
                            <?php foreach($fieldset['fields'] as $prop){ ?>
                                <?php if (isset($props_values[$prop['id']])) { ?>
                                <?php $prop_field = $props_fields[$prop['id']]; ?>
                                    <tr>
                                        <td class="title"><?php html($prop['title']); ?></td>
                                        <td class="value">
                                            <?php echo $prop_field->setItem($item)->parse($props_values[$prop['id']]); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php
        $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_item_html", $item);
        if ($hooks_html) { echo html_each($hooks_html); }
    ?>

    <?php if ($ctype['item_append_html']){ ?>
        <div class="append_html"><?php echo $ctype['item_append_html']; ?></div>
    <?php } ?>

    <div class="info_bar">
        <?php if (!empty($item['rating_widget'])){ ?>
            <div class="bar_item bi_rating">
                <?php echo $item['rating_widget']; ?>
            </div>
        <?php } ?>
        <div class="bar_item bi_share">
            <div class="share">
                <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                <script src="//yastatic.net/share2/share.js"></script>
                <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus,twitter,lj,tumblr,viber,whatsapp,skype,telegram" data-size="s"></div>
            </div>
        </div>
        <?php if (!$item['is_approved']){ ?>
            <div class="bar_item bi_not_approved">
                <?php echo LANG_CONTENT_NOT_APPROVED; ?>
            </div>
        <?php } ?>
    </div>

</div>