<!DOCTYPE html>
<html lang="<?php echo cmsCore::getLanguageName(); ?>">
    <head>
        <title><?php $this->title(); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php if (cmsUser::isLogged()) { ?>
            <?php $this->addMainTplJSName('messages'); ?>
        <?php } ?>
        <?php if ($config->debug && cmsUser::isAdmin()) { ?>
            <?php $this->addTplCSSName('debug'); ?>
        <?php } ?>
        <?php $this->head(); ?>
        <meta name="generator" content="InstantCMS" />
    </head>
    <body id="<?php echo $device_type; ?>_device_type">
        <?php if ($config->debug && cmsUser::isAdmin()){ ?>
            <div id="debug_block">
                <?php $this->renderAsset('ui/debug', array('core' => $core)); ?>
            </div>
        <?php } ?>
        <?php $messages = cmsUser::getSessionMessages(); ?>
        <script>
            $(function(){
            <?php if ($messages){ ?>
                <?php foreach($messages as $message){ ?>
                    toastr.<?php echo $message['class']; ?>('<?php echo $message['text']; ?>');
                 <?php } ?>
            <?php } ?>
            });
        </script>
        <?php $this->bottom(); ?>
    </body>
</html>