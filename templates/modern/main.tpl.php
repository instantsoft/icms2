<!DOCTYPE html>
<html lang="<?php echo cmsCore::getLanguageName(); ?>" class="h-100">
    <head>
        <title><?php $this->title(); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <meta name="generator" content="InstantCMS" />
        <?php $this->addMainTplCSSName([
            'vendors/bootstrap/bootstrap.min',
            'theme',
        ]); ?>
        <?php $this->addMainTplJSName('jquery', true); ?>
        <?php $this->addMainTplJSName('vendors/bootstrap/bootstrap.min'); ?>
        <?php $this->addMainTplJSName('core'); ?>
        <?php $this->addMainTplJSName('modal'); ?>
        <?php $this->head(true, false, true); ?>
    </head>
    <body id="<?php echo $device_type; ?>_device_type" class="d-flex flex-column h-100">
        <?php $this->renderLayoutChild('scheme', ['rows' => $rows]); ?>
        <?php $this->printJavascriptTags(); ?>
        <?php $this->bottom(); ?>
        <?php if ($config->debug && cmsUser::isAdmin()){ ?>
            <?php $this->renderAsset('ui/debug', ['core' => $core]); ?>
        <?php } ?>
    </body>
</html>