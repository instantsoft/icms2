<!DOCTYPE html>
<html lang="<?php echo cmsCore::getLanguageName(); ?>">
    <head>
        <title><?php $this->title(); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <meta name="generator" content="InstantCMS" />
        <?php $this->addMainTplCSSName([
            'vendors/bootstrap/bootstrap.min',
        ]); ?>
        <?php $this->addMainTplJSName('jquery', true); ?>
        <?php $this->addMainTplJSName('vendors/bootstrap/bootstrap.min'); ?>
        <?php $this->addMainTplJSName('core'); ?>
        <?php $this->addMainTplJSName('modal'); ?>
        <?php if ($config->debug && cmsUser::isAdmin()) { ?>
            <?php $this->addTplCSSName('debug'); ?>
        <?php } ?>

        <?php $this->head(true, false, true); ?>

    </head>
    <body id="<?php echo $device_type; ?>_device_type">

        <?php if (!$config->is_site_on){ ?>
            <div id="site_off_notice" class="bg-warning">
                <div class="container py-2 text-secondary">
                    <?php if (cmsUser::isAdmin()){ ?>
                        <?php printf(ERR_SITE_OFFLINE_FULL, href_to('admin', 'settings', 'siteon')); ?>
                    <?php } else { ?>
                        <?php echo ERR_SITE_OFFLINE; ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php $this->renderLayoutChild('scheme', ['rows' => $rows]); ?>

        <?php if ($config->debug && cmsUser::isAdmin()){ ?>
            <?php $this->renderAsset('ui/debug', array('core' => $core)); ?>
        <?php } ?>
        <?php $messages = cmsUser::getSessionMessages(); ?>
        <?php $this->printJavascriptTags(); ?>
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