<?php if ($widget->options['menu_type'] == 'navbar') { ?>
    <?php
        $nav_class = ['navbar p-0'];
        if ($widget->options['navbar_expand']) {
            $nav_class[] = $widget->options['navbar_expand'];
        }
        if ($widget->options['navbar_color_scheme']) {
            $nav_class[] = $widget->options['navbar_color_scheme'];
        }
        if ($widget->options['menu_navbar_style'] !== 'navbar-nav') {
            $widget->options['menu_navbar_style'] .= ' w-100';
        }
    ?>
    <nav class="<?php echo implode(' ', $nav_class); ?>">
        <?php if ($widget->options['toggler_icon']) { ?>
            <?php if ($widget->options['toggler_show_sitename']) { ?>
                <span class="navbar-brand icms-navbar-brand__show_on_hide">
                    <?php echo cmsConfig::get('sitename'); ?>
                </span>
            <?php } ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#target-<?php echo $widget->options['menu']; ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php } ?>
        <div class="collapse navbar-collapse<?php if (!$widget->options['navbar_expand']) { ?> show<?php } ?>" id="target-<?php echo $widget->options['menu']; ?>">
            <?php
                $this->menu(
                    $widget->options['menu'],
                    $widget->options['is_detect'],
                    $widget->options['menu_navbar_style'].(!empty($widget->options['class']) ? ' '.$widget->options['class'] : '').(!$widget->options['navbar_expand'] ? ' flex-row icms-navbar-expanded' : ''),
                    $widget->options['max_items'], empty($widget->options['is_detect_strict']),
                    (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
                    $widget->title
                );
            ?>
            <?php if ($widget->options['show_search_form']) { ?>
                <form class="form-inline<?php if ($widget->options['show_search_form'] == 2) { ?> icms-navbar-form__show_on_hide<?php } ?> ml-auto my-2 my-lg-0" action="<?php echo href_to('search'); ?>" method="get">
                    <div class="input-group">
                        <?php echo html_input('text', 'q', '', ['placeholder'=>ERR_SEARCH_TITLE, 'class' => 'border-0']); ?>
                        <div class="input-group-append">
                            <button class="btn bg-white text-muted" type="submit">
                                <?php html_svg_icon('solid', 'search'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </nav>
<?php } elseif($widget->options['menu_type'] == 'nav'){ ?>
    <?php
        $this->menu(
            $widget->options['menu'],
            $widget->options['is_detect'],
            $widget->options['menu_nav_style'].(!empty($widget->options['menu_nav_style_column']) ? ' '.$widget->options['menu_nav_style_column'] : '').(!empty($widget->options['class']) ? ' '.$widget->options['class'] : '').(!empty($widget->options['menu_is_pills']) ? ' nav-pills' : '').(!empty($widget->options['menu_is_fill']) ? ' '.$widget->options['menu_is_fill'] : ''),
            $widget->options['max_items'], empty($widget->options['is_detect_strict']),
            (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
            $widget->title
        );
    ?>
<?php } ?>
