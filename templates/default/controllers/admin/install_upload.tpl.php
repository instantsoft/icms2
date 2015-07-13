<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);
?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE; ?></h1>

<?php if ($errors){ ?>
    <ul class="errors">
        <?php foreach($errors as $error){ ?>
            <li>
                <div class="text"><?php echo $error['text']; ?></div>
                <div class="hint">
                    <?php if (isset($error['hint'])){ ?>
                        <strong><?php echo LANG_CP_INSTALL_ERR_HINT; ?>:</strong> <?php echo $error['hint']; ?><br/>
                    <?php } ?>
                    <?php if (isset($error['fix'])){ ?>
                        <strong><?php echo LANG_CP_INSTALL_ERR_FIX; ?>:</strong> <?php echo $error['fix']; ?><br/>
                    <?php } ?>
                    <?php if (isset($error['workaround'])){ ?>
                        <strong><?php echo LANG_CP_INSTALL_ERR_WA; ?>:</strong> <?php echo $error['workaround']; ?><br/>
                    <?php } ?>
                </div>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

<form action="" method="post" enctype="multipart/form-data">

    <?php echo html_csrf_token(); ?>

    <?php if ($errors){ ?>
        <?php echo html_input('hidden', 'is_no_extract', 1); ?>
    <?php } ?>

    <?php if (!$errors){ ?>

        <fieldset>

            <legend><?php echo LANG_CP_INSTALL_PACKAGE_FILE; ?></legend>

            <div class="field">
                <?php echo html_file_input('package'); ?>
                <div class="hint">
                    <?php echo LANG_CP_INSTALL_PACKAGE_FILE_HINT; ?>
                </div>
            </div>

        </fieldset>

    <?php } ?>

    <?php echo html_submit(LANG_CONTINUE); ?>

</form>
