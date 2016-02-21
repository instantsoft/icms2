<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);
?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE_DONE; ?></h1>

<p class="positive">
    <?php echo LANG_CP_INSTALL_PACKAGE_DONE_INFO; ?>
</p>

<?php if (!$is_cleared) { ?>
    <p class="negative">
        <?php echo sprintf(LANG_CP_INSTALL_PACKAGE_NOT_CLEARED, $path_relative); ?>
    </p>
<?php } ?>

<p>
    <?php echo html_button(LANG_CONTINUE, 'continue', "location.href='".$this->href_to($redirect_action)."'"); ?>
</p>

