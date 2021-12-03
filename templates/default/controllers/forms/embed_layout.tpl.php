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
            'theme-text', 'theme-layout', 'theme-gui', 'theme-content'
        ]);
        ?>
        <?php $this->addMainTplJSName('jquery', true); ?>
        <?php $this->addMainTplJSName([
            'core'
        ]); ?>
        <?php $this->head(); ?>
    </head>
    <body class="icms-forms__embed_layout <?php echo $device_type; ?>_device_type embed-form" onload="top.postMessage(JSON.stringify({id: 'embed-form-<?php echo $form_data['hash']; ?>', height: $('html').height()}), '*');">
        <?php $this->body(); ?>
        <?php $this->bottom(); ?>
        <script>
            $(function(){
                icms.events.on('icms_forms_submitajax', function (result){
                    top.postMessage(JSON.stringify({id: 'embed-form-<?php echo $form_data['hash']; ?>', height: $('html').height()}), '*');
                });
            });
        </script>
    </body>
</html>