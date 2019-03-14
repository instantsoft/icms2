<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->addMainCSS('templates/default/css/theme-modal.css'); ?>
    <?php $this->addMainCSS('templates/default/css/jquery-ui.css'); ?>
    <?php $this->addMainCSS('templates/default/css/animate.css'); ?>
    <?php $this->addMainJS('templates/default/js/jquery.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-ui.js'); ?>
    <?php $this->addMainJS('templates/default/js/i18n/jquery-ui/'.cmsCore::getLanguageName().'.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-ui.touch-punch.js'); ?>
    <?php $this->addMainJS('templates/default/js/jquery-modal.js'); ?>
    <?php $this->addMainJS('templates/default/js/core.js'); ?>
    <?php $this->head(false); ?>
</head>
<body id="widgets_layout">
    <div>
        <?php $this->body(); ?>
    </div>
    <script type="text/javascript">
        function widgetUpdated(widget, result){
            window.parent.location.reload();
        }
    </script>
</body>
</html>