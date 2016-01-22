<?php
    $config = cmsConfig::getInstance();
    $user = cmsUser::getInstance();
    $updater = new cmsUpdater();
    $update = $updater->checkUpdate(true);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->addMainCSS("templates/{$this->name}/css/theme-modal.css"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/jquery.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/jquery-modal.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/core.js"); ?>
    <?php $this->addMainJS("templates/{$this->name}/js/modal.js"); ?>
    <?php $this->head(false); ?>
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
                <li><a href="<?php echo href_to('users', $user->id); ?>" class="user"><?php echo $user->nickname; ?></a></li>
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

                <!-- Вывод тела -->
                <?php $this->body(); ?>

                <div class="pad"></div>

                <div class="loading-overlay"></div>

        </div>

    </div>

    <div id="cp_footer">
        <div class="container">
            <a href="http://www.instantcms.ru/">InstantCMS</a> v<?php echo cmsCore::getVersion(); ?> &mdash;
            &copy; <a href="http://www.instantsoft.ru/">InstantSoft</a> <?php echo date('Y'); ?> &mdash;
            <a href="<?php echo href_to('admin', 'credits'); ?>"><?php echo LANG_CP_3RDPARTY_CREDITS; ?></a>
        </div>
    </div>

    <script>

        function fitLayout(){
            var h1 = $('#cp_body h1').offset().top + $('#cp_body h1').height();
            var h2 = $('#cp_footer').offset().top;
            $('table.layout').height(h2 - h1 + 2);
            $('table.layout').width( $('#cp_body').width() + 40 );
        }

        $(document).ready(function(){
            fitLayout();
            window.onbeforeunload = function(){
                $('.loading-overlay').show();
            }
        });

        $(window).resize(function(){
            fitLayout();
        });

    </script>

</body>
</html>
