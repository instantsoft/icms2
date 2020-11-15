<html class="h-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php echo LANG_ERROR; ?></title>
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <?php
        $this->addMainTplCSSName([
            'theme'
        ]);
        ?>
        <?php $this->printCssTags(); ?>
    </head>
    <body class="flex-row d-flex align-items-center h-100 bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7" id="data-wrap">
                    <div class="d-flex align-items-center mb-3">
                        <h1 class="error display-3 my-0 d-flex mr-4 text-danger">
                            <b class="text-danger mr-2"><?php echo html_svg_icon('solid', 'exclamation-triangle'); ?></b>
                            503
                        </h1>
                        <div class="text-white">
                            <?php echo $message; ?>
                        </div>
                    </div>
                    <?php if ($details){ ?>
                        <p class="text-light"><?php echo nl2br($details); ?></p>
                    <?php } ?>

                    <?php if($is_debug){ ?>

                        <?php $stack = debug_backtrace(); ?>

                        <?php if(isset($stack[4])){ ?>

                            <p class="text-light"><b><?php echo LANG_TRACE_STACK; ?>:</b></p>

                            <ul id="trace_stack" class="text-white-50">

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

                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>