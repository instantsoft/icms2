<?php $config = cmsConfig::getInstance(); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo LANG_ERROR; ?></title>
    <?php $this->addMainTplCSSName([
        'theme-modal',
        'theme-gui',
        'theme-errors'
        ]); ?>
    <?php $this->addMainTplJSName('jquery', true); ?>
    <?php $this->addMainTplJSName([
        'jquery-modal',
        'core',
        'modal'
        ]); ?>
    <?php
    $this->printCssTags();
    $this->printJavascriptTags();
    ?>
</head>
<body id="error_body">
    <div id="site_error_wrap">

        <div id="errormsg"><?php echo $message; ?></div>

        <?php if ($details){ ?>
            <div class="pre"><?php echo nl2br($details); ?></div>
        <?php } ?>

        <?php if($is_debug){ ?>
            <?php $stack = debug_backtrace(); ?>
            <?php if(isset($stack[4])){ ?>
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
            <?php } ?>
        <?php } ?>
    </div>
</body>