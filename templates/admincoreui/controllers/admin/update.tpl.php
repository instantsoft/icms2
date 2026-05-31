<?php
    $this->setPageTitle(LANG_CP_SECTION_UPDATE);
    $this->addBreadcrumb(LANG_CP_SECTION_UPDATE, $this->href_to('update'));
?>

<?php if ($update === cmsUpdater::UPDATE_CHECK_ERROR){ ?>
    <div class="alert alert-info">
        <?php echo LANG_CP_UPDATE_CHECK_FAIL; ?>
    </div>
<?php } ?>

<?php if ($update === cmsUpdater::UPDATE_NOT_AVAILABLE){ ?>
    <div class="alert alert-info">
        <?php echo sprintf(LANG_CP_UPDATE_NOT_AVAILABLE, $current_version['version'], html_date($current_version['date'])); ?>
    </div>
<?php } ?>

<?php if (!empty($update['version'])) { ?>

<div class="card card-accent-success">
    <div class="card-header">
        <?php printf(LANG_CP_UPDATE_AVAILABLE, $update['version']); ?>
    </div>
    <div class="card-body">

        <h4><?php echo LANG_CP_UPDATE_DATE; ?>: <?php echo html_date($update['date']); ?></h4>

        <p class="mb-2 mt-3"><?php echo sprintf(LANG_CP_UPDATE_RELEASE_DESC, str_replace('.', '', $current_version['version'])); ?></p>

        <p class="mb-4"><?php echo LANG_CP_UPDATE_MANUAL_2; ?>.</p>

        <form action="<?php echo $this->href_to('install'); ?>" method="post" enctype="multipart/form-data">
            <?php echo html_csrf_token(); ?>
            <input type="hidden" name="package" value="<?php html($update['url']); ?>">
            <div class="d-flex align-items-center">
                <button class="button btn button-submit btn-success loading-icon update-install" type="submit" value="1" name="submit">
                    <span><?php echo LANG_CP_UPDATE_INSTALL; ?></span>
                </button>
                <div class="mx-2 text-muted"><?php echo LANG_OR; ?></div>
                <a href="<?php echo $update['url'];?>" class="btn btn-secondary">
                    <?php echo LANG_CP_UPDATE_DOWNLOAD; ?>
                </a>
            </div>
        </form>
    </div>
</div>
<?php ob_start(); ?>
<script>
    $('.update-install').on('click', function() {
        $(this).addClass('disabled is-busy');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>