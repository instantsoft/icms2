<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo ERR_FORBIDDEN; ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php $this->addMainTplCSSName([
            'vendors/simple-line-icons/css/simple-line-icons',
            'style'
        ]); ?>
        <?php $this->addCSS($this->getStylesFileName('admin')); ?>
        <?php $this->head(false); ?>
    </head>
    <?php if($show_login_link){ ?>
        <body class="app flex-row align-items-center bg-dark">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card-group">
                            <div class="card p-0 p-md-2 p-lg-4">
                                <div class="top-logo">
                                    <img src="<?php echo $this->getTemplateFilePath('images/logo.svg'); ?>" width="200" alt="InstantCMS Logo">
                                </div>
                                <div class="card-body">
                                    <h2 class="error text-dark"><?php echo LANG_LOGIN_ADMIN; ?></h2>
                                    <p class="text-muted"><?php echo LANG_PLEASE_LOGIN; ?></p>
                                    <form action="<?php echo href_to('auth', 'login'); ?>" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                                        <?php echo html_csrf_token(); ?>
                                        <?php echo html_input('hidden', 'submit', 1); ?>
                                        <?php echo html_input('hidden', 'back', href_to('admin')); ?>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon-user"></i>
                                                </span>
                                            </div>
                                            <input class="form-control" required="" type="email" name="login_email" placeholder="<?php echo LANG_EMAIL; ?>">
                                        </div>
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon-lock"></i>
                                                </span>
                                            </div>
                                            <input class="form-control" required="" type="password" name="login_password" placeholder="<?php echo LANG_PASSWORD; ?>">
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-row align-items-center">
                                                    <div class="col-auto">
                                                        <button class="btn btn-primary mb-2" type="submit"><?php echo LANG_LOG_IN; ?></button>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check text-muted mb-2">
                                                            <input name="remember" class="form-check-input" value="1" type="checkbox" id="remember">
                                                            <label class="form-check-label" for="remember">
                                                                <?php echo LANG_REMEMBER_ME; ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <a class="back-to-home mt-2 pl-4 pl-md-5" href="<?php echo href_to_home(); ?>">← <?php echo LANG_BACK_TO_HOME; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    <?php } else { ?>
        <body class="app flex-row align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="clearfix">
                            <h1 class="float-left display-3 mr-4 error">403</h1>
                            <h4 class="pt-3"><?php echo ERR_FORBIDDEN; ?></h4>
                            <?php if($message){ ?>
                                <p class="text-muted"><?php echo $message; ?></p>
                            <?php } ?>
                            <p class="text-muted"><a href="<?php echo href_to('admin'); ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    <?php } ?>
</html>