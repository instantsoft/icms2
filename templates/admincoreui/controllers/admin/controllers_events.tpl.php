<?php

    $this->setPageTitle(LANG_EVENTS_MANAGEMENT);
    $this->addBreadcrumb(LANG_EVENTS_MANAGEMENT);

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());

    $this->addMenuItem('breadcrumb-menu', [
        'title'   => LANG_HELP,
        'url'     => LANG_HELP_URL_EVENTS,
        'options' => [
            'target' => '_blank',
            'icon'   => 'question-circle'
        ]
    ]);
?>

<?php if (!empty($events_delete)){ ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo LANG_EVENTS_DELETED;?></h4>
            <hr>
            <?php foreach ($events_delete as $controller => $events){ ?>
                <h5 class="mt-2">
                    <?php echo string_lang($controller.'_CONTROLLER', $controller);?>
                </h5>
                <ul class="mb-0">
                <?php foreach ($events as $event_name){ ?>
                    <li><?php echo $event_name;?></li>
                <?php } ?>
                </ul>
            <?php }?>
        </div>
<?php } ?>

<?php if (!empty($events_add)){ ?>

        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading"><?php echo LANG_EVENTS_ALLOW_NEW;?></h4>
            <hr>
            <?php foreach ($events_add as $controller => $events){ ?>
                <h5 class="mt-2">
                    <?php echo string_lang($controller.'_CONTROLLER', $controller);?>
                </h5>
                <ul class="mb-0">
                <?php foreach ($events as $event_name){ ?>
                    <li><?php echo $event_name;?></li>
                <?php } ?>
                </ul>
            <?php }?>
        </div>

<?php } ?>
<div class="alert alert-warning">
    <?php echo LANG_EVENTS_MANAGEMENT_HINT; ?>
</div>
<?php echo $grid_html;
