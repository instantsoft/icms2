<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php
    $this->addMainTplJSName('jquery', true);
    $this->addMainTplJSName([
        'jquery-ui',
        'i18n/jquery-ui/'.cmsCore::getLanguageName(),
        'jquery-ui.touch-punch',
        'jquery-modal',
        'core'
        ]);
    $this->addMainTplCSSName([
        'theme-modal',
        'jquery-ui',
        'animate'
        ]);
     ?>
    <?php $this->head(false); ?>
</head>
<body id="widgets_layout">
    <div>
        <?php $this->body(); ?>
    </div>
    <script>
        function widgetUpdated(widget, result){
            window.parent.location.reload();
        }
    </script>
    <?php $this->bottom(); ?>
</body>
</html>