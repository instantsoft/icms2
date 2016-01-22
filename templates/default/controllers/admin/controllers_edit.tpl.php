<h1><?php echo LANG_CP_SECTION_CONTROLLERS; ?>: <span><?php echo $controller_title; ?></span></h1>

<?php
    $this->setPageTitle($controller_title);
    $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));
    $this->addBreadcrumb($controller_title, $this->href_to('controllers', 'edit/'.$controller_name));
?>

<?php if (!$is_backend){ ?>
    <p><?php echo sprintf(LANG_CP_ERR_BACKEND_NOT_FOUND, $controller_title); ?></p>
<?php } ?>

<?php if ($is_backend){ ?>

    <?php if (isset($this->menus['backend'])){ ?>
        <div class="pills-menu">
            <?php $this->menu('backend'); ?>
        </div>
    <?php } ?>

    <?php echo $backend_controller->runAction($action_name, $params); ?>

<?php } ?>

