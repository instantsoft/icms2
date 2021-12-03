<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php $this->title(); ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php
        $this->addMainTplCSSName([
            'theme'
        ]);
        ?>
        <?php $this->addMainTplJSName('jquery', true); ?>
        <?php $this->addMainTplJSName('vendors/popper.js/js/popper.min'); ?>
        <?php $this->addMainTplJSName('vendors/bootstrap/bootstrap.min'); ?>
        <?php $this->addMainTplJSName([
            'core'
        ]); ?>
        <?php $this->printCssTags(); ?>
    </head>
    <body class="h-100 w-100 overflow-hidden <?php echo $device_type; ?>_device_type embed-form" onload="top.postMessage(JSON.stringify({id: 'embed-form-<?php echo $form_data['hash']; ?>', height: $('html').height()}), '*');">
        <?php $this->body(); ?>
        <?php $this->printJavascriptTags(); ?>
        <?php $this->bottom(); ?>
        <script>
            icms.events.on('icms_forms_submitajax', function (result){
                top.postMessage(JSON.stringify({id: 'embed-form-<?php echo $form_data['hash']; ?>', height: $('html').height()}), '*');
            });
        </script>
    </body>
</html>