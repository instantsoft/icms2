<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo LANG_ERROR; ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php $this->addMainTplCSSName([
            'vendors/simple-line-icons/css/simple-line-icons',
            'style'
            ]); ?>
        <?php $this->head(false); ?>
    </head>
    <body class="app flex-row align-items-center bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7" id="data-wrap">
                    <div class="clearfix">
                        <h1 class="error float-left display-3 mr-4 text-danger">503</h1>
                        <p class="pt-3 text-danger"><b><i class="icon-fire icons font-2xl"></i> <?php echo $message; ?></b></p>
                        <?php if ($details){ ?>
                                <p class="text-muted"><?php echo nl2br($details); ?></p>
                        <?php } ?>
                    </div>
                    <?php $stack = debug_backtrace(); ?>
                    <?php if(!isset($stack[4])){ return; } ?>

                    <p><b><?php echo LANG_TRACE_STACK; ?>:</b></p>

                    <ul id="trace_stack">

                        <?php for($i=4; $i<=14; $i++){ ?>

                            <?php if (!isset($stack[$i])){ break; } ?>

                            <?php $row = $stack[$i]; ?>
                            <li>
                                <b>
                                    <?php if (isset($row['class'])) { ?>
                                        <?php echo $row['class'] . $row['type'] . $row['function'] . '()'; ?>
                                    <?php } else { ?>
                                        <?php echo $row['function'] . '()'; ?>
                                    <?php } ?>
                                </b>
                                <?php if (isset($row['file'])) { ?>
                                    <span>@ <?php echo str_replace(cmsConfig::get('root_path'), '/', $row['file']); ?></span> : <span><?php echo $row['line']; ?></span>
                                <?php } ?>
                            </li>

                        <?php } ?>

                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>