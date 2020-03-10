<?php

    $this->setPageTitle(LANG_CP_SECTION_UPDATE);
    $this->addBreadcrumb(LANG_CP_SECTION_UPDATE, $this->href_to('update'));

?>

<?php if ($update === cmsUpdater::UPDATE_CHECK_ERROR){ ?>
    <div class="alert alert-info" role="alert">
        <?php echo LANG_CP_UPDATE_CHECK_FAIL; ?>
    </div>
<?php } ?>

<?php if ($update === cmsUpdater::UPDATE_NOT_AVAILABLE){ ?>
    <div class="alert alert-info" role="alert">
        <?php echo sprintf(LANG_CP_UPDATE_NOT_AVAILABLE, $current_version['version'], html_date($current_version['date'])); ?>
    </div>
<?php } ?>

<?php if (!empty($update['version'])){ ?>

<div class="card card-accent-success">
    <div class="card-header">
        <?php printf(LANG_CP_UPDATE_AVAILABLE, $update['version']); ?>
    </div>
    <div class="card-body">

        <h4><?php echo LANG_CP_UPDATE_DATE; ?>: <?php echo html_date($update['date']); ?></h4>

        <?php if (!function_exists('curl_init')){ ?>

            <div class="alert alert-danger" role="alert">
                <?php echo LANG_CP_UPDATE_MANUAL_1; ?><br>
                <?php echo LANG_CP_UPDATE_MANUAL_2; ?><br>
                <a href="<?php echo $update['url'];?>"><?php echo LANG_CP_UPDATE_DOWNLOAD; ?></a>
            </div>

        <?php } else { ?>

            <a class="btn btn-success mt-3 loading-icon" href="<?php echo $this->href_to('update', 'install');?>" onclick="return icms.admin.goToLinkAnimated(this)">
                <?php echo LANG_CP_UPDATE_INSTALL; ?>
            </a>

        <?php } ?>

    </div>
</div>

<?php } ?>