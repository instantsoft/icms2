<div class="photo_filter">
    <form action="<?php echo $item['base_url']; ?>" method="get">
    <span title="<?php echo LANG_SORTING; ?>" class="box_menu <?php echo !isset($item['filter_selected']['ordering']) ?'': 'box_menu_select'; ?>">
        <?php echo $item['filter_panel']['ordering'][$item['filter_values']['ordering']]; ?>
    </span>
    <div class="box_menu_dd">
        <?php foreach($item['filter_panel']['ordering'] as $value => $name){ ?>
            <?php $url_params = $item['url_params']; $url_params['ordering'] = $value; ?>
            <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>">
                <?php echo $name; ?>
                <?php if($item['filter_values']['ordering'] == $value){ ?>
                    <input type="hidden" name="ordering" value="<?php echo $value; ?>">
                    <i class="check">&larr;</i>
                <?php } ?>
            </a>
        <?php } ?>
    </div>
    <span title="<?php echo LANG_PHOTOS_SORT_ORDERTO; ?>" class="box_menu <?php echo !isset($item['filter_selected']['orderto']) ?'': 'box_menu_select'; ?>">
        <?php echo $item['filter_panel']['orderto'][$item['filter_values']['orderto']]; ?>
    </span>
    <div class="box_menu_dd">
        <?php foreach($item['filter_panel']['orderto'] as $value => $name){ ?>
            <?php $url_params = $item['url_params']; $url_params['orderto'] = $value; ?>
            <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>">
                <?php echo $name; ?>
                <?php if($item['filter_values']['orderto'] == $value){ ?>
                    <input type="hidden" name="orderto" value="<?php echo $value; ?>">
                    <i class="check">&larr;</i>
                <?php } ?>
            </a>
        <?php } ?>
    </div>
    <?php if($item['filter_panel']['type']){ ?>
        <span class="box_menu <?php echo !isset($item['filter_selected']['type']) ?'': 'box_menu_select'; ?>">
            <?php echo $item['filter_panel']['type'][$item['filter_values']['type']]; ?>
        </span>
        <div class="box_menu_dd">
            <?php foreach($item['filter_panel']['type'] as $value => $name){ ?>
                <?php $url_params = $item['url_params']; $url_params['type'] = $value; ?>
                <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>">
                    <?php echo $name; ?>
                    <?php if($item['filter_values']['type'] == $value){ ?>
                        <input type="hidden" name="type" value="<?php echo $value; ?>">
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
            <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>">
                <?php echo $name; ?>
                <?php if($item['filter_values']['orientation'] == $value){ ?>
                    <input type="hidden" name="orientation" value="<?php echo $value; ?>">
                    <i class="check">&larr;</i>
                <?php } ?>
            </a>
        <?php } ?>
    </div>

    <?php if($item['filter_values']['width'] || $item['filter_values']['height']){ ?>
        <span class="box_menu box_menu_select">
            <?php echo LANG_PHOTOS_MORE_THAN; ?>
            <?php if($item['filter_values']['width'] && $item['filter_values']['height']){ ?>
                <?php html($item['filter_values']['width']); ?>px
            X
                <?php html($item['filter_values']['height']); ?>px
            <?php } elseif($item['filter_values']['width']){ ?>
                <?php html($item['filter_values']['width']); ?>px <?php echo LANG_PHOTOS_BYWIDTH; ?>
            <?php } elseif($item['filter_values']['height']){ ?>
                <?php html($item['filter_values']['height']); ?>px <?php echo LANG_PHOTOS_BYHEIGHT; ?>
            <?php } ?>
        </span>
    <?php } else { ?>
        <span class="box_menu"><?php echo LANG_PHOTOS_SIZE; ?></span>
    <?php } ?>

    <div class="box_menu_dd">
        <div class="size_search_params">
            <fieldset>
                <legend><?php echo LANG_PHOTOS_MORE_THAN; ?></legend>
                <div class="field">
                    <label><?php echo LANG_PHOTOS_SIZE_W; ?></label>
                    <input type="text" name="width" value="<?php html($item['filter_values']['width']); ?>" placeholder="px" class="input">
                </div>
                <div class="field">
                    <label><?php echo LANG_PHOTOS_SIZE_H; ?></label>
                    <input type="text" name="height" value="<?php html($item['filter_values']['height']); ?>" placeholder="px" class="input">
                </div>
            </fieldset>
            <div class="buttons">
                <input type="submit" class="button" value="<?php echo LANG_FIND; ?>">
            </div>
        </div>
    </div>

    <?php if($item['filter_selected']) { ?>
        <a title="<?php echo LANG_PHOTOS_CLEAR_FILTER; ?>" class="box_menu clear_filter" href="<?php echo $page_url; ?>">x</a>
    <?php } ?>

    </form>
</div>