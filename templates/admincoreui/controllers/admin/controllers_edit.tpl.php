<?php if (!$is_backend){ ?>
    <p class="alert alert-info mt-4"><?php echo sprintf(LANG_CP_ERR_BACKEND_NOT_FOUND, $controller_title); ?></p>
<?php } ?>

<?php if ($is_backend){ ?>

    <?php if (isset($this->menus['backend']) && empty($this->menus['admin_toolbar'])){ ?>
        <?php $this->setMenuItems('admin_toolbar', $this->menus['backend']); ?>
    <?php } ?>

    <?php echo $html; ?>

<?php }
