<?php $config = cmsConfig::getInstance(); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo LANG_ERROR; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-gui.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/default/css/theme-errors.css">
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/jquery-modal.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/core.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/default/js/modal.js"></script>
</head>
<body id="error_body">
    <div id="site_error_wrap">

        <div id="errormsg"><?php echo $message; ?></div>

        <?php if ($details){ ?>
                <div class="pre"><?php echo nl2br($details); ?></div>
        <?php } ?>

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
</body>