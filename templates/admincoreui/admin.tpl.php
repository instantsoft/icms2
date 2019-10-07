<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php if ($config->debug){ ?>
        <?php $this->addTplCSSName('debug'); ?>
    <?php } ?>
    <?php $this->head(false); ?>
</head>
<body class="app">
    <?php if ($config->debug){ ?>
        <div id="debug_block">
            <?php $this->renderAsset('ui/debug', array('core' => cmsCore::getInstance())); ?>
        </div>
    <?php } ?>
    <?php $this->bottom(); ?>
</body>
</html>