<?php
    $config = cmsConfig::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ERR_FORBIDDEN; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-modal.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-gui.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-errors.css">
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery-modal.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/core.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/modal.js"></script>
</head>
<body id="body403">

    <div id="error403">
        <h1>403</h1>
        <h2><?php echo ERR_FORBIDDEN; ?></h2>
        <?php if($message){ ?>
            <h3><?php echo $message; ?></h3>
        <?php } ?>
        <p><a href="<?php echo $config->host; ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
    </div>

    <?php if($show_login_link){ ?>
        <div id="error-maintenance-footer">
            <span>
                <a class="ajaxlink ajax-modal" title="<?php echo LANG_LOGIN_ADMIN; ?>" href="<?php echo href_to('auth', 'login'); ?>">
                    <?php echo LANG_LOGIN_ADMIN; ?>
                </a>
            </span>
        </div>
    <?php } ?>

</body>
