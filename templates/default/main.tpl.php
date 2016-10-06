<?php $core = cmsCore::getInstance(); ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-text.css"); ?>
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-layout.css"); ?>
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-gui.css"); ?>
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-widgets.css"); ?>
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-content.css"); ?>
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-modal.css"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/jquery.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/jquery-modal.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/core.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/modal.js"); ?>
    <?php if (cmsUser::isLogged()){ ?>
        <?php $this->addMainJS("templates/{$this->name}/js/messages.js"); ?>
    <?php } ?>
    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
    <?php $this->head(); ?>
    <style><?php include('options.css.php'); ?></style>
</head>
<body id="<?php echo $device_type; ?>_device_type">

    <div id="layout">

        <?php if (!$config->is_site_on){ ?>
            <div id="site_off_notice"><?php printf(ERR_SITE_OFFLINE_FULL, href_to('admin', 'settings', 'siteon')); ?></div>
        <?php } ?>

        <header>
            <div id="logo">
                <?php if($core->uri) { ?>
                    <a href="<?php echo href_to_home(); ?>"></a>
                <?php } else { ?>
                    <span></span>
                <?php } ?>
            </div>
            <div class="widget_ajax_wrap" id="widget_pos_header"><?php $this->widgets('header', false, 'wrapper_plain'); ?></div>
        </header>

        <?php if($this->hasWidgetsOn('top')) { ?>
            <nav>
                <div class="widget_ajax_wrap" id="widget_pos_top"><?php $this->widgets('top', false, 'wrapper_plain'); ?></div>
            </nav>
        <?php } ?>

        <div id="body">

            <?php
                $is_sidebar = $this->hasWidgetsOn('right-top', 'right-center', 'right-bottom');
                $section_width = $is_sidebar ? '730px' : '100%';
            ?>

            <?php
                $messages = cmsUser::getSessionMessages();
                if ($messages){
                    ?>
                    <div class="sess_messages">
                        <?php
                            foreach($messages as $message){
                                echo $message;
                            }
                        ?>
                    </div>
                    <?php
                }
            ?>

            <section style="width:<?php echo $section_width; ?>">

                <div class="widget_ajax_wrap" id="widget_pos_left-top"><?php $this->widgets('left-top'); ?></div>

                <?php if ($this->isBody()){ ?>
                    <article>
                        <?php if ($config->show_breadcrumbs && $this->isBreadcrumbs()){ ?>
                            <div id="breadcrumbs">
                                <?php $this->breadcrumbs(array('strip_last'=>false)); ?>
                            </div>
                        <?php } ?>
                        <div id="controller_wrap"><?php $this->body(); ?></div>
                    </article>
                <?php } ?>

                <div class="widget_ajax_wrap" id="widget_pos_left-bottom"><?php $this->widgets('left-bottom'); ?></div>

            </section>

            <?php if($is_sidebar){ ?>
                <aside>
                    <div class="widget_ajax_wrap" id="widget_pos_right-top"><?php $this->widgets('right-top'); ?></div>
                    <div class="widget_ajax_wrap" id="widget_pos_right-center"><?php $this->widgets('right-center'); ?></div>
                    <div class="widget_ajax_wrap" id="widget_pos_right-bottom"><?php $this->widgets('right-bottom'); ?></div>
                </aside>
            <?php } ?>

        </div>

        <?php if ($config->debug && cmsUser::isAdmin()){ ?>
            <div id="sql_debug" style="display:none">
                <div id="sql_queries">
                    <div id="sql_stat"><?php echo $core->db->getStat(); ?></div>
                    <?php foreach($core->db->query_list as $sql) { ?>
                        <div class="query">
                            <div class="src"><?php echo $sql['src']; ?></div>
                            <?php echo nl2br(htmlspecialchars($sql['sql'])); ?>
                            <div class="query_time"><?php echo LANG_DEBUG_QUERY_TIME; ?> <span class="<?php echo (($sql['time']>=0.1) ? 'red_query' : 'green_query'); ?>"><?php echo number_format($sql['time'], 5); ?></span> <?php echo LANG_SECOND10 ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <footer>
            <ul>
                <li id="copyright">
                    <a href="<?php echo $this->options['owner_url'] ? $this->options['owner_url'] : href_to_home(); ?>">
                        <?php html($this->options['owner_name'] ? $this->options['owner_name'] : cmsConfig::get('sitename')); ?></a>
                    &copy;
                    <?php echo $this->options['owner_year'] ? $this->options['owner_year'] : date('Y'); ?>
                </li>
                <li id="info">
                    <span class="item">
                        <?php echo LANG_POWERED_BY_INSTANTCMS; ?>
                    </span>
                    <span class="item">
                        <?php echo LANG_ICONS_BY_FATCOW; ?>
                    </span>
                    <?php if ($config->debug && cmsUser::isAdmin()){ ?>
                        <span class="item">
                            SQL: <a href="#sql_debug" title="SQL dump" class="ajax-modal"><?php echo $core->db->query_count; ?></a>
                        </span>
                        <?php if ($config->cache_enabled){ ?>
                            <span class="item">
                                Cache: <a href="<?php echo href_to('admin', 'cache_delete', $config->cache_method);?>" title="Clear cache"><?php echo cmsCache::getInstance()->query_count; ?></a>
                            </span>
                        <?php } ?>
                        <span class="item">
                            Mem: <?php echo round(memory_get_usage()/1024/1024, 2); ?> Mb
                        </span>
                        <span class="item">
                            Time: <?php echo number_format(cmsCore::getTime(), 4); ?> s
                        </span>
                    <?php } ?>
                </li>
                <li id="nav">
                    <div class="widget_ajax_wrap" id="widget_pos_footer"><?php $this->widgets('footer', false, 'wrapper_plain'); ?></div>
                </li>
            </ul>
        </footer>

    </div>

</body>
</html>
