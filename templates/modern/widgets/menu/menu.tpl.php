<?php if ($widget->options['menu_type'] == 'navbar') { ?>
    <?php
        $nav_class = ['navbar p-0'];
        if ($widget->options['navbar_expand']) {
            $nav_class[] = $widget->options['navbar_expand'];
        }
        if ($widget->options['navbar_color_scheme']) {
            $nav_class[] = $widget->options['navbar_color_scheme'];
        }
        $site_name = html(cmsConfig::get('sitename'), false);
    ?>
    <nav class="<?php echo implode(' ', $nav_class); ?>">
        <?php if ($widget->options['toggler_icon']) { ?>
            <?php if ($widget->options['toggler_show_sitename'] && empty($widget->options['toggler_show_logo'])) { ?>
                <span class="navbar-brand icms-navbar-brand__show_on_hide">
                    <?php echo $site_name; ?>
                </span>
            <?php } ?>
            <?php if (!empty($widget->options['toggler_show_logo'])) { ?>
                <<?php if($core->uri) { ?>a href="<?php echo href_to_home(); ?>"<?php } else { ?>span<?php } ?> class="navbar-brand flex-shrink-0">
                    <img src="<?php echo $logos['small_logo']; ?>" class="d-sm-none" alt="<?php echo $site_name; ?>">
                    <img src="<?php echo $logos['logo']; ?>" class="d-none d-sm-block" alt="<?php echo $site_name; ?>">
                    <?php if ($widget->options['toggler_show_sitename']) { ?>
                        <?php echo $site_name; ?>
                    <?php } ?>
                </<?php if($core->uri) { ?>a<?php } else { ?>span<?php } ?>>
            <?php } ?>
            <button class="navbar-toggler" type="button" aria-label="<?php echo LANG_MENU; ?>" data-toggle="collapse" data-target="#target-<?php echo $widget->options['menu']; ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php } ?>
        <div class="collapse<?php if (!empty($widget->options['toggler_right_menu'])) { ?> ml-auto flex-grow-0<?php } ?> navbar-collapse<?php if (!$widget->options['navbar_expand']) { ?> show<?php } ?>" id="target-<?php echo $widget->options['menu']; ?>">
            <?php
                $navbar_class = ['navbar-nav'];
                if (!empty($widget->options['menu_nav_style'])) {
                    $navbar_class[] = $widget->options['menu_nav_style']. ' w-100';
                }
                if (!empty($widget->options['menu_nav_style_add'])) {
                    $navbar_class[] = $widget->options['menu_nav_style_add'];
                }
                if (!empty($widget->options['class'])) {
                    $navbar_class[] = $widget->options['class'];
                }
                if (empty($widget->options['navbar_expand'])) {
                    $navbar_class[] = 'flex-row icms-navbar-expanded';
                }

                $this->menu(
                    $widget->options['menu'],
                    $widget->options['is_detect'],
                    implode(' ', $navbar_class),
                    $widget->options['max_items'], empty($widget->options['is_detect_strict']),
                    (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
                    $widget->title
                );
            ?>
            <?php if ($widget->options['show_search_form']) { ?>
                <form class="form-inline<?php if ($widget->options['show_search_form'] == 2) { ?> icms-navbar-form__show_on_hide<?php } ?> ml-auto my-2 my-lg-0" action="<?php echo href_to('search'); ?>" method="get">
                    <div class="input-group">
                        <?php echo html_input('text', 'q', '', ['placeholder'=>ERR_SEARCH_TITLE, 'autocomplete' => 'off']); ?>
                        <div class="input-group-append">
                            <button class="btn" type="submit">
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

        $nav_class = ['nav'];
        if ($widget->options['navbar_color_scheme']) {
            $nav_class[] = $widget->options['navbar_color_scheme'];
        }
        if (!empty($widget->options['menu_nav_style'])) {
            $nav_class[] = $widget->options['menu_nav_style'];
        }
        if (!empty($widget->options['menu_nav_style_add'])) {
            $nav_class[] = $widget->options['menu_nav_style_add'];
        }
        if (!empty($widget->options['class'])) {
            $nav_class[] = $widget->options['class'];
        }
        if (!empty($widget->options['menu_is_pills'])) {
            $nav_class[] = 'nav-pills';
        }
        if (!empty($widget->options['menu_is_fill'])) {
            $nav_class[] = $widget->options['menu_is_fill'];
        }

        $this->menu(
            $widget->options['menu'],
            $widget->options['is_detect'],
            implode(' ', $nav_class),
            $widget->options['max_items'], empty($widget->options['is_detect_strict']),
            (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
            $widget->title
        );
    ?>
<?php } ?>
