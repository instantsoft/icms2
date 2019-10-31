<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo ERR_PAGE_NOT_FOUND; ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php $this->addMainTplCSSName([
            'vendors/simple-line-icons/css/simple-line-icons',
            'style'
            ]); ?>
        <?php $this->head(false); ?>
    </head>
    <body class="app flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6" id="data-wrap">
                    <div class="clearfix">
                        <h1 class="float-left display-3 mr-4 error">404</h1>
                        <h4 class="pt-3"><?php echo ERR_PAGE_NOT_FOUND; ?></h4>
                        <p class="text-muted"><a href="<?php echo href_to('admin'); ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
