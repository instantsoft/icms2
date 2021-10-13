<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->addMainTplCSSName([
        'vendors/font-awesome/css/font-awesome.min',
        'vendors/simple-line-icons/css/simple-line-icons',
        'vendors/toastr/toastr.min',
        'jquery-ui',
        'style'
    ]); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName([
        'jquery-cookie',
        'jquery-ui',
        'jquery-ui.touch-punch',
        'i18n/jquery-ui/'.cmsCore::getLanguageName(),
        'vendors/popper.js/js/popper.min',
        'vendors/bootstrap/bootstrap.min',
        'vendors/perfect-scrollbar/js/perfect-scrollbar.min',
        'vendors/@coreui/coreui/js/coreui.min',
        'vendors/toastr/toastr.min',
        'core',
        'modal',
        'admin-core'
    ]); ?>
    <?php $this->head(false); ?>
</head>
<body id="widgets_layout" class="m-3" onload="top.postMessage($('html').height(), '*');" onresize="top.postMessage($('html').height(), '*');">
    <?php $this->body(); ?>
    <script>
        function widgetUpdated(widget, result){
            window.parent.location.reload();
        }
        $(function(){
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('body').trigger('resize');
            });
        });
    </script>
    <?php $this->bottom(); ?>
</body>
</html>