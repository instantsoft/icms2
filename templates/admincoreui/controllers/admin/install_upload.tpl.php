<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_INSTALL,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

?>

<?php if ($errors){ ?>
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?php echo LANG_ERROR;?></h4>
        <hr>
        <ul>
        <?php foreach($errors as $error){ ?>
            <li>
                <h5 class="mt-2"><?php echo $error['text']; ?></h5>
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
    </div>
<?php } ?>
<div class=" card mb-0 form-tabs">
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">

            <?php echo html_csrf_token(); ?>

            <?php if ($errors){ ?>
                <?php echo html_input('hidden', 'is_no_extract', 1); ?>
            <?php } ?>

            <?php if (!$errors){ ?>

                <fieldset>
                    <div class="field form-group">
                        <label><?php echo LANG_CP_INSTALL_PACKAGE_FILE; ?></label>
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" name="package" class="custom-file-input" id="package" accept="application/zip">
                                <label class="custom-file-label" for="package" data-browse="<?php echo LANG_SELECT; ?>"><?php echo LANG_PARSER_FILE; ?></label>
                            </div>
                        </div>
                        <div class="form-text hint text-muted small mt-1">
                            <?php echo LANG_CP_INSTALL_PACKAGE_FILE_HINT; ?>
                        </div>
                    </div>
                    <p><?php echo mb_strtoupper(LANG_OR); ?></p>
                    <div class="field form-group">
                        <label><?php echo LANG_CP_INSTALL_BY_LINK; ?></label>
                        <?php echo html_input('text', 'package', ''); ?>
                        <div class="form-text hint text-muted small mt-1">
                            <?php echo LANG_CP_INSTALL_PACKAGE_LINK_HINT; ?>
                        </div>
                    </div>
                </fieldset>

            <?php } else { ?>
                <p><?php echo sprintf(LANG_CP_INSTALL_NOT_WRITABLE_CUSTOM, $this->site_config->upload_root.$this->controller->installer_upload_path); ?></p>
            <?php } ?>

            <div class="buttons mt-3">
                <?php echo html_submit(LANG_CONTINUE); ?>
                <?php echo html_button(LANG_CANCEL, 'cancel', "location.href='".$this->href_to('addons_list')."'"); ?>
            </div>

        </form>
    </div>
</div>