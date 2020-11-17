<html class="h-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo ERR_FORBIDDEN; ?></title>
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
            'core',
            'modal'
        ]); ?>
        <?php $this->printCssTags(); ?>
    </head>
    <body class="h-100 flex-row d-flex align-items-center bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <h1 class="display-3 my-0 mr-4 text-danger">
                            <b class="text-danger"><?php echo html_svg_icon('solid', 'key'); ?></b>
                            403
                        </h1>
                        <h3 class="m-0 text-white"><?php echo ERR_FORBIDDEN; ?></h3>
                    </div>
                        <?php if($message){ ?>
                            <p class="text-white"><?php echo $message; ?></p>
                        <?php } ?>
                        <div>
                            <a class="text-muted" href="<?php echo href_to_home(); ?>">
                                <?php echo LANG_BACK_TO_HOME; ?>
                            </a>
                        </div>
                </div>
            </div>
        </div>
        <?php if($show_login_link){ ?>
            <div class="position-fixed fixed-bottom pb-2">
                <a class="text-center text-muted d-block ajaxlink ajax-modal" title="<?php echo LANG_LOGIN_ADMIN; ?>" href="<?php echo href_to('auth', 'login'); ?>">
                    <?php echo LANG_LOGIN_ADMIN; ?>
                </a>
            </div>
        <?php } ?>
        <?php $this->printJavascriptTags(); ?>
        <?php $this->bottom(); ?>
    </body>
</html>