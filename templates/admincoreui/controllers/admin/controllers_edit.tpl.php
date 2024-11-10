<?php if (!$is_backend){ ?>
    <p class="alert alert-info mt-4"><?php echo sprintf(LANG_CP_ERR_BACKEND_NOT_FOUND, $controller_title); ?></p>
<?php return; } ?>

<?php if (isset($this->menus['backend']) && empty($this->menus['admin_toolbar'])){ ?>
    <?php $this->setMenuItems('admin_toolbar', $this->menus['backend']); ?>
<?php } ?>

<?php if ($html === false){ ?>
    <p class="alert alert-danger mt-4"><?php echo LANG_ACCESS_DENIED; ?></p>
<?php } else { ?>
    <?php echo $html; ?>
<?php } ?>