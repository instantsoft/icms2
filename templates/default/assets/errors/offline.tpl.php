<?php
    $config = cmsConfig::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ERR_SITE_OFFLINE; ?> &mdash; <?php echo $config->sitename; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-errors.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-modal.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-gui.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-text.css">
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery-modal.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/core.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/modal.js"></script>
</head>
<body>

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

    <div id="error-maintenance">
        <h1><?php echo ERR_SITE_OFFLINE; ?></h1>
        <?php if ($reason) { ?>
            <p><?php echo $reason; ?></p>
        <?php } ?>
    </div>

    <div id="error-maintenance-footer">
        <span>
            <a class="ajaxlink ajax-modal" href="<?php echo href_to('auth', 'login'); ?>"><?php echo LANG_LOGIN_ADMIN; ?></a>
        </span>
    </div>

</body>
