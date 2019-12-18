<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->addMainTplCSSName([
        'theme-modal',
        'jquery-ui',
        'animate'
        ]); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName([
        'jquery-ui',
        'i18n/jquery-ui/'.cmsCore::getLanguageName(),
        'jquery-ui.touch-punch',
        'jquery-modal',
        'core',
        'modal',
        'messages',
        'admin-core'
        ]); ?>
    <?php if ($config->debug){ ?>
        <?php $this->addTplCSSName('debug'); ?>
    <?php } ?>
    <?php $this->head(false); ?>
</head>
<body>

    <div id="wrapper">

        <div id="cp_top_line">
            <ul id="left_links">
                <?php if (!$config->is_site_on){ ?>
                    <li><div id="site_off_notice"><?php printf(ERR_SITE_OFFLINE_FULL, href_to('admin', 'settings', 'siteon')); ?></div></li>
                <?php } ?>
                <li>
                    <?php if (!empty($update['version'])) { ?>
                        <div id="is_update">
                            <a href="<?php echo href_to('admin', 'update'); ?>"><?php printf(LANG_CP_UPDATE_AVAILABLE, $update['version']); ?></a>
                        </div>
                    <?php } else { ?>
                        <a href="<?php echo href_to('admin', 'update'); ?>"><?php echo LANG_CP_UPDATE_CHECK; ?></a>
                    <?php } ?>
                </li>
                <?php if($this->controller->install_folder_exists){ ?>
                    <li id="install_folder_exists">
                        <?php echo LANG_CP_INSTALL_FOLDER_EXISTS; ?>
                    </li>
                <?php } ?>
            </ul>
            <ul id="right_links">
                <li><a href="<?php echo href_to('users', $user->id); ?>" class="user"><?php echo html_avatar_image($user->avatar, 'micro'); ?><span><?php echo $user->nickname; ?></span></a></li>
                <?php if($notices_count){ ?>
                    <li class="bell ajax-modal notices-counter">
                        <a href="<?php echo href_to('messages', 'notices'); ?>" title="<?php echo LANG_ADMIN_NOTICES; ?>">
                            <span class="wrap"><?php echo LANG_ADMIN_NOTICES; ?><span class="counter"><?php echo $notices_count; ?></span></span>
                        </a>
                    </li>
                <?php } ?>
                <li><a href="<?php echo LANG_HELP_URL; ?>"><?php echo LANG_HELP; ?></a></li>
                <li><a href="<?php echo href_to_home(); ?>"><?php echo LANG_CP_BACK_TO_SITE; ?></a></li>
                <li><a href="<?php echo href_to('auth', 'logout'); ?>" class="logout"><?php echo LANG_LOG_OUT; ?></a></li>
            </ul>
        </div>

        <div id="cp_header">
            <div id="logo"><a href="<?php echo href_to('admin'); ?>"></a></div>
            <div id="menu"><?php $this->menu('cp_main'); ?></div>
        </div>

        <?php if($this->isBreadcrumbs()){ ?>
            <div id="cp_pathway">
                <?php $this->breadcrumbs(array('home_url' => href_to('admin'), 'strip_last'=>false, 'separator'=>'<div class="sep"></div>')); ?>
            </div>
        <?php } ?>

        <div id="cp_body">

                <!-- Сообщения сессии -->
                <?php
                $messages = cmsUser::getSessionMessages();
                if ($messages){ ?>
                    <div class="sess_messages animated fadeIn">
                        <?php foreach($messages as $message){ ?>
                            <div class="message_<?php echo $message['class']; ?>"><?php echo $message['text']; ?></div>
                         <?php } ?>
                    </div>
                <?php } ?>

                <!-- Вывод тела -->
                <?php $this->body(); ?>

                <div class="pad"></div>

        </div>

    </div>

    <div id="cp_footer">
        <div class="container">
            <a href="https://instantcms.ru/">InstantCMS</a> v<?php echo cmsCore::getVersion(); ?> &mdash;
            &copy; <a href="http://www.instantsoft.ru/">InstantSoft</a> <?php echo date('Y'); ?> &mdash;
            <a href="<?php echo href_to('admin', 'credits'); ?>"><?php echo LANG_CP_3RDPARTY_CREDITS; ?></a>
            <?php if ($config->debug){ ?>
                 &mdash;
                <span class="item">
                    <a href="#debug_block" title="<?php echo LANG_DEBUG; ?>" class="ajax-modal"><?php echo LANG_DEBUG; ?></a>
                </span> &mdash;
                <span class="item">
                    Time: <?php echo cmsDebugging::getTime('cms', 4); ?> s
                </span> &mdash;
                <span class="item">
                    Mem: <?php echo round(memory_get_usage(true)/1024/1024, 2); ?> Mb
                </span>
            <?php } ?>
        </div>
    </div>
    <?php if ($config->debug){ ?>
        <div id="debug_block">
            <?php $this->renderAsset('ui/debug', array('core' => cmsCore::getInstance())); ?>
        </div>
    <?php } ?>
    <script type="text/javascript">
        $(function(){
        <?php if(empty($this->options['disable_help_anim'])){ ?>
            setTimeout(function(){
                $('.cp_toolbar li.help').addClass('animated shake');
                $(document).tooltip({
                    items: '.cp_toolbar li.help',
                    show: { duration: 0 },
                    hide: { duration: 0 },
                    content: function() {
                        return '<?php echo LANG_CP_TOOLTIP_HELP; ?><span class="anim_tooltip"><?php echo LANG_CP_TOOLTIP_HELP_HINT; ?></span>';
                    },
                    position: {
                        my: "center",
                        at: "top-40"
                    }
                });
            }, 1000);
        <?php } ?>
        });
    </script>
    <?php $this->bottom(); ?>
</body>
</html>