<?php
    $user = cmsUser::getInstance();
    $updater = new cmsUpdater();
    $update = $updater->checkUpdate(true);
    $notices_count = cmsCore::getModel('messages')->getNoticesCount($user->id);
    if($this->controller->install_folder_exists){
        cmsUser::addSessionMessage(LANG_CP_INSTALL_FOLDER_EXISTS, 'error');
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->addMainCSS('templates/default/css/theme-modal.css'); ?>
    <?php $this->addMainCSS('templates/default/css/jquery-ui.css'); ?>
    <?php $this->addMainCSS('templates/default/css/animate.css'); ?>
    <?php $this->addMainJS('templates/default/js/jquery.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-ui.js'); ?>
    <?php $this->addMainJS('templates/default/js/i18n/jquery-ui/'.cmsCore::getLanguageName().'.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-ui.touch-punch.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-modal.js'); ?>
    <?php $this->addMainJS('templates/default/js/core.js'); ?>
    <?php $this->addMainJS('templates/default/js/modal.js'); ?>
    <?php $this->addMainJS("templates/default/js/messages.js"); ?>
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
                    if ($messages){
                        ?>
                        <div class="sess_messages animated fadeIn">
                            <?php
                                foreach($messages as $message){
                                    echo $message;
                                }
                            ?>
                        </div>
                        <?php
                    }
                ?>

                <!-- Вывод тела -->
                <?php $this->body(); ?>

                <div class="pad"></div>

        </div>

    </div>

    <div id="cp_footer">
        <div class="container">
            <a href="http://www.instantcms.ru/">InstantCMS</a> v<?php echo cmsCore::getVersion(); ?> &mdash;
            &copy; <a href="http://www.instantsoft.ru/">InstantSoft</a> <?php echo date('Y'); ?> &mdash;
            <a href="<?php echo href_to('admin', 'credits'); ?>"><?php echo LANG_CP_3RDPARTY_CREDITS; ?></a>
        </div>
    </div>

    <script type="text/javascript">
        var fit_layout_delta = 0;
        function fitLayout(){
            var h1 = $('#cp_body h1').offset().top + $('#cp_body h1').height();
            var h2 = $('#cp_footer').offset().top;
            $('table.layout').height(h2 - h1 - 2 + fit_layout_delta);
            $('table.layout').width( $('#cp_body').width() + 40 );
        }
        toolbarScroll = {
            win: null,
            toolbar: null,
            spacer: null,
            spacer_init: false,
            offset: 0,
            init: function (){
                this.win     = $(window);
                this.toolbar = $('.cp_toolbar');
                if(this.toolbar.length == 0){
                    return;
                }
                this.offset  = (this.toolbar).offset().top;
                if((+$('#wrapper').height() - +$(this.win).height()) <= (this.offset + 20)){
                    return;
                }
                if(this.spacer_init === false){
                    $(this.toolbar).after($('<div id="fixed_toolbar_spacer" />').height(40).hide());
                    this.spacer = $('#fixed_toolbar_spacer');
                    this.spacer_init = true;
                }
                this.run();
            },
            run: function (){
                handler = function (){
                    toolbarScroll.doAutoScroll();
                };
                this.win.off('scroll', handler).on('scroll', handler).trigger('scroll');
            },
            doAutoScroll: function (){
                scroll_top = this.win.scrollTop();
                if (scroll_top > this.offset) {
                    if(!$(this.toolbar).hasClass('fixed_toolbar')){
                        $(this.toolbar).addClass('fixed_toolbar');
                        $(this.spacer).show();
                        fit_layout_delta = 30; fitLayout();
                    }
                } else {
                    $(this.toolbar).removeClass('fixed_toolbar');
                    $(this.spacer).hide();
                    fit_layout_delta = 0; fitLayout();
                }
            }
        };
        $(function(){
            $(window).on('resize', function (){
                toolbarScroll.init();
                fitLayout();
            });
            toolbarScroll.init();
            fitLayout();
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
            icms.events.on('datagrid_rows_loaded', function (result){
                fitLayout();
                toolbarScroll.init();
            });
        });

    </script>
</body>
</html>