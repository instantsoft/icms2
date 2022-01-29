<html class="min-vh-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo ERR_PAGE_NOT_FOUND; ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php $this->addMainTplCSSName([
            'theme'
        ]); ?>
        <?php $this->printCssTags(); ?>
        <link rel="icon" href="<?php echo $this->getTemplateFilePath('images/favicons/favicon.ico'); ?>" type="image/x-icon">
    </head>
    <body class="d-flex min-vh-100 align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <img src="<?php echo $this->getTemplateFilePath('images/404.svg', true); ?>" alt="404" />
                </div>
                <div class="col-md-6 align-self-center">
                    <h1 class="display-1">404</h1>
                    <h2><?php echo ERR_PAGE_NOT_FOUND; ?></h2>
                    <?php if(cmsController::enabled('search')){ ?>
                        <form action="<?php echo href_to('search'); ?>" method="get" class="my-4">
                            <div class="input-group">
                                <?php echo html_input('text', 'q', '', array('placeholder'=> ERR_SEARCH_QUERY_INPUT)); ?>
                                <div class="input-group-append">
                                    <button type="submit" name="submit" class="btn btn-secondary"><?php echo ERR_SEARCH_TITLE; ?></button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                    <a class="btn btn-primary" href="<?php echo href_to_home(); ?>"><?php echo LANG_BACK_TO_HOME; ?></a>
                </div>
            </div>
        </div>
    </body>
</html>