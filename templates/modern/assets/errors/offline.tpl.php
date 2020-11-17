<?php $config = cmsConfig::getInstance(); ?>
<html class="h-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo ERR_SITE_OFFLINE; ?> &mdash; <?php echo $config->sitename; ?></title>
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
    <body class="h-100 flex-row d-flex align-items-center bg-secondary">
        <section class="container">
            <?php $messages = cmsUser::getSessionMessages(); if ($messages){ foreach($messages as $message){ ?>
            <div class="alert alert-<?php echo str_replace(['error'], ['danger'], $message['class']); ?> alert-dismissible fade show" role="alert">
                <?php echo $message['text']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php } } ?>
            <main class="row justify-content-center">
                <div class="col-md-6 text-center text-white">
                    <div class="display-3">
                        <b class="text-warning">
                            <?php echo html_svg_icon('solid', 'tools'); ?>
                        </b>
                    </div>
                    <h1><?php echo ERR_SITE_OFFLINE; ?></h1>
                    <?php if ($reason) { ?>
                        <p><?php echo $reason; ?></p>
                    <?php } ?>
                </div>
            </main>
        </section>
        <footer class="position-fixed fixed-bottom pb-2">
            <a class="text-center text-white d-block ajaxlink ajax-modal" title="<?php echo LANG_LOGIN_ADMIN; ?>" href="<?php echo href_to('auth', 'login'); ?>">
                <?php echo LANG_LOGIN_ADMIN; ?>
            </a>
        </footer>
        <?php $this->printJavascriptTags(); ?>
        <?php $this->bottom(); ?>
    </body>
</html>