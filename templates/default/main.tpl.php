<!DOCTYPE html>
<html>
<head>
    <title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->addMainTplCSSName('theme-text'); ?>
    <?php $this->addMainTplCSSName('theme-layout'); ?>
    <?php $this->addMainTplCSSName('theme-gui'); ?>
    <?php $this->addMainTplCSSName('theme-widgets'); ?>
    <?php $this->addMainTplCSSName('theme-content'); ?>
    <?php $this->addMainTplCSSName('theme-modal'); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName('jquery-modal'); ?>
    <?php $this->addMainTplJSName('core'); ?>
    <?php $this->addMainTplJSName('modal'); ?>
    <?php if ($config->debug && cmsUser::isAdmin()){ ?>
        <?php $this->addTplCSSName('debug'); ?>
    <?php } ?>
    <?php if(cmsUser::isAdmin()){ ?>
        <?php $this->addTplJSName('widgets'); ?>
    <?php } ?>
    <?php $this->head(); ?>
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <meta name="generator" content="InstantCMS" />
    <style><?php include('options.css.php'); ?></style>
</head>
<body id="<?php echo $device_type; ?>_device_type">

    <div id="layout">

        <?php if (!$config->is_site_on){ ?>
            <div id="site_off_notice">
                <?php if (cmsUser::isAdmin()){ ?>
                    <?php printf(ERR_SITE_OFFLINE_FULL, href_to('admin', 'settings', 'siteon')); ?>
                <?php } else { ?>
                    <?php echo ERR_SITE_OFFLINE; ?>
                <?php } ?>
            </div>
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
            if ($messages){ ?>
                <div class="sess_messages">
                    <?php foreach($messages as $message){ ?>
                        <div class="message_<?php echo $message['class']; ?>"><?php echo $message['text']; ?></div>
                     <?php } ?>
                </div>
            <?php } ?>

            <section style="width:<?php echo $section_width; ?>">

                <div class="widget_ajax_wrap" id="widget_pos_left-top"><?php $this->widgets('left-top'); ?></div>

                <?php if ($this->isBody()){ ?>
                    <article>
                        <?php if ($config->show_breadcrumbs && $core->uri && $this->isBreadcrumbs()){ ?>
                            <div id="breadcrumbs">
                                <?php $this->breadcrumbs(array('strip_last'=>false)); ?>
                            </div>
                        <?php } ?>
                        <div id="controller_wrap">
                            <?php $this->block('before_body'); ?>
                            <?php $this->body(); ?>
                        </div>
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
            <div id="debug_block">
                <?php $this->renderAsset('ui/debug', array('core' => $core)); ?>
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
                    <?php if ($config->debug && cmsUser::isAdmin()){ ?>
                        <span class="item">
                            <a href="#debug_block" title="<?php echo LANG_DEBUG; ?>" class="ajax-modal"><?php echo LANG_DEBUG; ?></a>
                        </span>
                        <span class="item">
                            Time: <?php echo cmsDebugging::getTime('cms', 4); ?> s
                        </span>
                        <span class="item">
                            Mem: <?php echo round(memory_get_usage(true)/1024/1024, 2); ?> Mb
                        </span>
                    <?php } ?>
                </li>
                <li id="nav">
                    <div class="widget_ajax_wrap" id="widget_pos_footer"><?php $this->widgets('footer', false, 'wrapper_plain'); ?></div>
                </li>
            </ul>
        </footer>

    </div>
    <?php $this->bottom(); ?>
</body>
</html>