<?php
    $config = cmsConfig::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ERR_FORBIDDEN; ?></title>
    <?php $this->addMainTplCSSName([
        'theme-modal',
        'theme-gui',
        'theme-errors'
        ]); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName([
        'jquery-modal',
        'core',
        'modal'
        ]); ?>
    <?php
    $this->printCssTags();
    $this->printJavascriptTags();
    ?>
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
