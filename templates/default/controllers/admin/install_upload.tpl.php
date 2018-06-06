<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);

	$this->addToolButton(array(
		'class'  => 'help',
        'title'  => LANG_HELP,
        'target' => '_blank',
        'href'   => LANG_HELP_URL_INSTALL
    ));

    $this->addToolButton(array(
        'class' => 'addons',
        'title' => LANG_CP_OFICIAL_ADDONS,
        'href'  => $this->href_to('addons_list')
    ));

?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE; ?></h1>

<div class="cp_toolbar">
    <?php $this->toolbar(); ?>
</div>

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
            <p><?php echo mb_strtoupper(LANG_OR); ?></p>
            <div class="field">
                <label><?php echo LANG_CP_INSTALL_BY_LINK; ?></label>
                <?php echo html_input('text', 'package', ''); ?>
                <div class="hint">
                    <?php echo LANG_CP_INSTALL_PACKAGE_LINK_HINT; ?>
                </div>
            </div>

        </fieldset>

    <?php } ?>

    <div class="buttons">
        <?php if (!$errors){ ?>
            <?php echo html_submit(LANG_CONTINUE); ?>
        <?php } ?>
        <?php echo html_button(LANG_CANCEL, 'cancel', "location.href='".$this->href_to('addons_list')."'"); ?>
    </div>

</form>