<!DOCTYPE html>
<html class="min-vh-100">
<head>
	<title><?php $this->title(); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->addMainTplCSSName([
        'vendors/simple-line-icons/css/simple-line-icons',
        'vendors/toastr/toastr.min',
        'jquery-ui',
        'style'
    ]); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName([
        'jquery-cookie',
        'jquery-ui',
        'jquery-ui.touch-punch',
        'i18n/jquery-ui/'.cmsCore::getLanguageName(),
        'vendors/popper.js/js/popper.min',
        'vendors/bootstrap/bootstrap.min',
        'vendors/perfect-scrollbar/js/perfect-scrollbar.min',
        'vendors/@coreui/coreui/js/coreui.min',
        'vendors/toastr/toastr.min',
        'core',
        'modal',
        'admin-core'
    ]); ?>
    <?php if ($config->debug){ ?>
        <?php $this->addMainTplJSName(['vendors/list.min']); ?>
    <?php } ?>
    <?php $this->onDemandTplJSName([
        'vendors/photoswipe/photoswipe.min',
        'vendors/introjs/intro.min',
    ]); ?>
    <?php $this->onDemandTplCSSName([
        'photoswipe', 'vendors/introjs/introjs.min', 'vendors/introjs/themes/introjs-modern'
    ]); ?>
    <link rel="shortcut icon" href="<?php echo $this->getTemplateFilePath('images/favicons/favicon_admin.ico'); ?>">
    <?php $this->head(false); ?>
</head>
<?php $messages = cmsUser::getSessionMessages(); ?>
<body class="icms-cpanel app header-fixed sidebar-fixed <?php if(!$close_sidebar){ ?>sidebar-lg-show<?php } ?> <?php if($hide_sidebar){ ?> brand-minimized sidebar-minimized<?php } ?> <?php echo $device_type; ?>_device_type" data-device="<?php echo $device_type; ?>">
    <header class="app-header navbar shadow-sm" id="cp_header">
        <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?php echo href_to('admin'); ?>">
            <img class="navbar-brand-full" src="<?php echo $this->getTemplateFilePath('images/logo.svg', true); ?>" width="135" alt="InstantCMS Logo">
            <img class="navbar-brand-minimized" src="<?php echo $this->getTemplateFilePath('images/small_logo.svg', true); ?>" width="40" height="40" alt="InstantCMS Logo">
        </a>
        <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-current_state="<?php if($close_sidebar){ ?>1<?php } else { ?>0<?php } ?>" data-toggle="sidebar-lg-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="nav navbar-nav d-md-down-none">
            <li class="nav-item px-3">
                <?php if (!empty($update['version'])) { ?>
                    <a class="nav-link text-warning" href="<?php echo href_to('admin', 'update'); ?>">
                        <span class="sk-spinner sk-spinner-pulse bg-warning"></span>
                        <?php printf(LANG_CP_UPDATE_AVAILABLE, $update['version']); ?>
                    </a>
                <?php } else { ?>
                    <a class="nav-link" data-toggle="tooltip" data-placement="bottom" href="<?php echo href_to('admin', 'update'); ?>" title="<?php echo LANG_CP_UPDATE_CHECK; ?>">
                        <?php html_svg_icon('solid', 'code-branch'); ?> <?php echo cmsCore::getVersion(); ?>
                    </a>
                <?php } ?>
            </li>
            <?php if (!$config->is_site_on){ ?>
                <li class="nav-item px-3" id="site_off_notice">
                    <span class="btn btn-warning">
                        <?php html_svg_icon('solid', 'exclamation-triangle'); ?>
                        <?php printf(ERR_SITE_OFFLINE_FULL, href_to('admin', 'settings', 'siteon')); ?>
                    </span>
                </li>
            <?php } ?>
            <li class="nav-item px-3 text-light" title="<?php echo LANG_TODAY; ?>" data-toggle="tooltip" data-placement="bottom">
                <?php html_svg_icon('solid', 'calendar'); ?> <span><?php echo lang_date(date('d F')); ?></span> <span id="clock"></span>
            </li>
        </ul>
        <ul class="nav navbar-nav ml-auto">
            <?php if ($config->is_user_change_lang && count($langs) > 1){ ?>
                <li class="nav-item dropdown">
                    <a class="nav-link text-warning font-weight-bold dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                        <?php echo strtoupper($current_lang); ?>
                    </a>
                    <div class="dropdown-menu">
                        <?php foreach ($langs as $lang) { ?>
                            <a class="dropdown-item<?php if($lang === $current_lang){ ?> active<?php } ?>" href="<?php html($config->root . ($config->language === $lang ? '' : $lang.'/').$core->uri.($core->uri_query ? '?'.http_build_query($core->uri_query) : '')); ?>">
                                <?php echo strtoupper($lang); ?>
                            </a>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
            <li class="nav-item d-sm-down-none">
                <a data-toggle="dropdown" id="notices_counter" class="nav-link d-flex justify-content-center text-light" href="<?php echo href_to('admin', 'messages_notices'); ?>" title="<?php echo LANG_ADMIN_NOTICES; ?>" data-toggle="tooltip" data-placement="left">
                    <?php html_svg_icon('solid', 'bell'); ?>
                    <?php if($notices_count){ ?>
                        <span class="badge badge-pill badge-danger"><?php echo $notices_count; ?></span>
                    <?php } ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-xl" id="pm_notices_list">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
            </li>
            <li class="nav-item d-sm-down-none">
                <a class="nav-link d-flex justify-content-center text-light" href="<?php echo href_to_home(); ?>" target="_blank" title="<?php echo LANG_CP_BACK_TO_SITE; ?>" data-toggle="tooltip" data-placement="bottom">
                    <?php html_svg_icon('solid', 'share-square'); ?>
                </a>
            </li>
            <li class="nav-item d-md-down-none">
                <a class="nav-link d-flex justify-content-center text-light" href="<?php echo LANG_HELP_URL; ?>" target="_blank" title="<?php echo LANG_HELP; ?>" data-toggle="tooltip" data-placement="bottom">
                    <?php html_svg_icon('solid', 'question-circle'); ?>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link icms-user-avatar ml-3 mr-4 d-flex align-items-center" data-toggle="dropdown" href="#">
                    <?php if($user->avatar){ ?>
                        <img class="img-avatar" src="<?php echo html_avatar_image_src($user->avatar, 'micro'); ?>" alt="<?php html($user->nickname); ?>">
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($user->nickname, 'avatar__mini'); ?>
                    <?php } ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-header text-center">
                        <strong><?php echo $user->nickname; ?></strong>
                    </div>
                    <a class="dropdown-item" href="<?php echo href_to_profile($user); ?>">
                        <?php html_svg_icon('solid', 'user'); ?> <?php echo LANG_MY_PROFILE; ?>
                    </a>
                    <a class="dropdown-item" href="<?php echo href_to('admin', 'users', ['edit', $user->id]); ?>">
                        <?php html_svg_icon('solid', 'edit'); ?> <?php echo LANG_EDIT; ?>
                    </a>
                    <a class="dropdown-item" href="<?php echo href_to('auth', 'logout', false, ['csrf_token' => cmsForm::getCSRFToken()]); ?>">
                        <?php html_svg_icon('solid', 'sign-out-alt'); ?> <?php echo LANG_LOG_OUT; ?>
                    </a>
                </div>
            </li>
        </ul>
    </header>
    <div class="app-body">
        <div class="sidebar" id="cp_left_sidebar">
            <nav class="sidebar-nav">
                <?php $this->menu('cp_main', true, '', 0, true); ?>
                <div class="nav-title mt-3">
                    <a class="ajax-modal text-white" href="<?php echo href_to('admin', 'settings', ['sys_info']); ?>" title="<?php echo LANG_CP_DASHBOARD_SYSINFO; ?>">
                        <?php echo LANG_CP_SU; ?> <?php html_svg_icon('solid', 'info-circle'); ?>
                    </a>
                </div>
                <?php foreach ($su as $sukey => $su_item) { ?>
                    <div class="nav-item px-3 d-compact-none d-minimized-none" id="su-<?php echo $sukey; ?>">
                        <div class="text-uppercase mb-1">
                            <small>
                                <b><?php echo $su_item['title']; ?></b>
                            </small>
                        </div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-<?php echo $su_item['style']; ?>" role="progressbar" style="width: <?php echo $su_item['percent']; ?>%" aria-valuenow="<?php echo $su_item['percent']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted"><?php echo $su_item['hint']; ?></small>
                    </div>
                <?php } ?>
            </nav>
            <button class="sidebar-minimizer brand-minimizer" type="button" data-current_state="<?php if($hide_sidebar){ ?>1<?php } else { ?>0<?php } ?>"></button>
        </div>
        <main class="main" id="wrapper">
            <?php $this->breadcrumbs(array('home_url' => href_to('admin'), 'strip_last'=>false, 'separator'=>'')); ?>
            <?php if($this->hasMenu('admin_toolbar')){ ?>
                <nav class="bg-white mt-n4 border-bottom mb-4" id="admin_toolbar">
                    <div class="container-fluid py-2">
                        <?php $this->menu('admin_toolbar', true, 'nav nav-pills icms-menu-hovered'); ?>
                    </div>
                </nav>
            <?php } ?>
            <div class="container-fluid print-body-wrap">
                <?php if ($messages){ ?>
                    <?php foreach($messages as $message){ ?>
                        <?php if (empty($message['is_keep'])){ continue; } ?>
                        <div class="alert alert-<?php echo str_replace('error', 'danger', $message['class']); ?>" role="alert">
                            <?php echo $message['text']; ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                     <?php } ?>
                <?php } ?>
                <?php $this->body(); ?>
            </div>
        </main>
    </div>
    <footer class="app-footer" id="cp_footer">
        <div class="d-none d-md-block">
            <?php if ($config->debug){ ?>
                <span class="item">
                    <a href="#debug_block" data-style="xl" class="ajax-modal" title="<?php echo LANG_DEBUG; ?>">
                        <?php echo LANG_DEBUG; ?>
                    </a>
                </span> &mdash;
                <span class="item">
                    Time: <?php echo cmsDebugging::getTime('cms', 4); ?> s
                </span> &mdash;
                <span class="item">
                    Mem: <?php echo round(memory_get_usage(true)/1024/1024, 2); ?> Mb
                </span>
            <?php } ?>
        </div>
        <div class="ml-auto mr-auto mr-md-0">
            <a href="https://instantcms.ru/">InstantCMS</a> &copy;  <?php echo date('Y'); ?> &mdash;
            <a href="<?php echo href_to('admin', 'credits'); ?>"><?php echo LANG_CP_3RDPARTY_CREDITS; ?></a>
        </div>
    </footer>
    <?php if ($config->debug){ ?>
        <?php $this->renderAsset('ui/debug', ['core' => cmsCore::getInstance(), 'hide_short_info' => true]); ?>
    <?php } ?>
    <script>
        <?php echo $this->getLangJS('LANG_BACK', 'LANG_NEXT', 'LANG_SKIP', 'LANG_DONE', 'LANG_LOADING', 'LANG_ALL'); ?>
        $(function(){
        <?php if($this->controller->install_folder_exists){ ?>
            toastr.error('<?php echo LANG_CP_INSTALL_FOLDER_EXISTS; ?>');
        <?php } ?>
        <?php if ($messages){ ?>
            <?php foreach($messages as $message){ ?>
                <?php if (!empty($message['is_keep'])){ continue; } ?>
                toastr.<?php echo $message['class']; ?>('<?php echo str_replace(["\n", "'"], ' ', $message['text']); ?>');
             <?php } ?>
        <?php } ?>
        });
    </script>
    <?php $this->bottom(); ?>
    <?php $this->onDemandPrint(); ?>
</body>
</html>