<?php

    $this->setPageTitle(LANG_EVENTS_MANAGEMENT);

    $this->addBreadcrumb(LANG_EVENTS_MANAGEMENT);

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());

    if (!empty($events_add) || !empty($events_delete)){

        $this->addToolButton(array(
            'class' => 'refresh',
            'title' => LANG_EVENTS_REFRESH,
            'href'  => $this->href_to('controllers', array('events_update'))
        ));

    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_EVENTS,
        'options' => [
            'target' => '_blank',
            'icon' => 'icon-question'
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

<?php $this->renderGrid($this->href_to('controllers', array('events_ajax')), $grid);
