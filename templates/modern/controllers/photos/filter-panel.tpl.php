<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-start mb-3 mb-md-4">
    <button class="navbar-toggler align-items-center" type="button" data-toggle="collapse" data-target="#photo_filter_nav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <span class="d-lg-none ml-3"><?php echo LANG_SHOW_FILTER; ?></span>
    <div class="collapse navbar-collapse" id="photo_filter_nav">
        <form action="<?php echo $item['base_url']; ?>" method="get">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a title="<?php echo LANG_SORTING; ?>" class="nav-link dropdown-toggle <?php echo !isset($item['filter_selected']['ordering']) ?'': 'active'; ?>" href="#" role="button" data-toggle="dropdown">
                    <?php echo $item['filter_panel']['ordering'][$item['filter_values']['ordering']]; ?>
                </a>
                <div class="dropdown-menu">
                    <?php foreach($item['filter_panel']['ordering'] as $value => $name){ ?>
                        <?php $url_params = $item['url_params']; $url_params['ordering'] = $value; ?>
                        <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>" class="dropdown-item">
                            <?php echo $name; ?>
                            <?php if($item['filter_values']['ordering'] == $value){ ?>
                                <input type="hidden" name="ordering" value="<?php echo $value; ?>">
                                <i class="check">&larr;</i>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a title="<?php echo LANG_PHOTOS_SORT_ORDERTO; ?>" class="nav-link dropdown-toggle <?php echo !isset($item['filter_selected']['orderto']) ?'': 'active'; ?>" href="#" role="button" data-toggle="dropdown">
                    <?php echo $item['filter_panel']['orderto'][$item['filter_values']['orderto']]; ?>
                </a>
                <div class="dropdown-menu">
                    <?php foreach($item['filter_panel']['orderto'] as $value => $name){ ?>
                        <?php $url_params = $item['url_params']; $url_params['orderto'] = $value; ?>
                        <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>" class="dropdown-item">
                            <?php echo $name; ?>
                            <?php if($item['filter_values']['orderto'] == $value){ ?>
                                <input type="hidden" name="orderto" value="<?php echo $value; ?>">
                                <i class="check">&larr;</i>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
            <?php if($item['filter_panel']['type']){ ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo !isset($item['filter_selected']['type']) ?'': 'active'; ?>" href="#" role="button" data-toggle="dropdown">
                        <?php echo $item['filter_panel']['type'][$item['filter_values']['type']]; ?>
                    </a>
                    <div class="dropdown-menu">
                        <?php foreach($item['filter_panel']['type'] as $value => $name){ ?>
                            <?php $url_params = $item['url_params']; $url_params['type'] = $value; ?>
                            <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>" class="dropdown-item">
                                <?php echo $name; ?>
                                <?php if($item['filter_values']['type'] == $value){ ?>
                                    <input type="hidden" name="type" value="<?php echo $value; ?>">
                                    <i class="check">&larr;</i>
                                <?php } ?>
                            </a>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo !isset($item['filter_selected']['orientation']) ?'': 'active'; ?>" href="#" role="button" data-toggle="dropdown">
                    <?php echo $item['filter_panel']['orientation'][$item['filter_values']['orientation']]; ?>
                </a>
                <div class="dropdown-menu">
                    <?php foreach($item['filter_panel']['orientation'] as $value => $name){ ?>
                        <?php $url_params = $item['url_params']; $url_params['orientation'] = $value; ?>
                        <a href="<?php echo $page_url.'?'.http_build_query($url_params); ?>" class="dropdown-item">
                            <?php echo $name; ?>
                            <?php if($item['filter_values']['orientation'] == $value){ ?>
                                <input type="hidden" name="orientation" value="<?php echo $value; ?>">
                                <i class="check">&larr;</i>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <?php if($item['filter_values']['width'] || $item['filter_values']['height']){ ?>
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
                    <?php } else { ?>
                        <?php echo LANG_PHOTOS_SIZE; ?>
                    <?php } ?>
                </a>
                <div class="dropdown-menu py-0">
                    <div class="px-4 py-3">
                        <div class="form-group">
                            <label class="text-nowrap"><?php echo LANG_PHOTOS_SIZE_W; ?>, <?php echo mb_strtolower(LANG_PHOTOS_MORE_THAN); ?></label>
                            <input type="text" name="width" autocomplete="off" value="<?php html($item['filter_values']['width']); ?>" placeholder="px" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="text-nowrap"><?php echo LANG_PHOTOS_SIZE_H; ?>, <?php echo mb_strtolower(LANG_PHOTOS_MORE_THAN); ?></label>
                            <input type="text" name="height" autocomplete="off" value="<?php html($item['filter_values']['height']); ?>" placeholder="px" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo LANG_FIND; ?></button>
                    </div>
                </div>
            </li>
            <?php if($item['filter_selected']) { ?>
                <li class="nav-item">
                    <a title="<?php echo LANG_PHOTOS_CLEAR_FILTER; ?>" data-toggle="tooltip" data-placement="top" class="nav-link text-danger" href="<?php echo $page_url; ?>">
                        <?php html_svg_icon('solid', 'window-close'); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        </form>
    </div>
</nav>