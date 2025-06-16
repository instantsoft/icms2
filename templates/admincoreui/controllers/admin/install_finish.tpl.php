<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE, $this->href_to('install'));
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE_DONE);

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());
?>

<div class="alert alert-success">
    <h4 class="alert-heading"><?php echo LANG_CP_INSTALL_PACKAGE_DONE_INFO;?></h4>
    <hr>
    <a class="btn btn-success" href="<?php echo $this->href_to($redirect_action); ?>"><?php echo LANG_CONTINUE; ?></a>
</div>

<?php if (!$is_cleared) { ?>
    <div class="alert alert-danger">
        <h4 class="alert-heading"><?php echo sprintf(LANG_CP_INSTALL_PACKAGE_NOT_CLEARED, $path_relative); ?></h4>
    </div>
<?php } ?>

<?php if ($undeleted_files) { ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading"><?php echo LANG_CP_INSTALL_PACKAGE_MANUAL_DELETE; ?></h4>
        <hr>
        <ul class="list-unstyled">
            <?php foreach ($undeleted_files as $path) { ?>
                <li><?php echo $path; ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>