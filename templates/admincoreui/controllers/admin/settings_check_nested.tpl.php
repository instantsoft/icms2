<?php

$this->setPageTitle(LANG_CP_CHECK_NESTED);

$this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
$this->addBreadcrumb(LANG_CP_CHECK_NESTED);

$this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

$this->addMenuItem('breadcrumb-menu', [
    'title' => LANG_HELP,
    'url'   => LANG_HELP_URL_CHECK_NESTED,
    'options' => [
        'target' => '_blank',
        'icon' => 'question-circle'
    ]
]);
?>

<?php if(!$nested_tables_exists){ ?>
    <p class="alert alert-warning mt-4" role="alert">
        <?php echo LANG_CP_NS_NO_TABLES; ?>
    </p>
<?php return; } ?>

<?php if($successful){ ?>
    <div class="alert alert-success mt-4" role="alert">
        <h4 class="alert-heading"><?php echo LANG_CP_NS_SUCCESSFUL; ?></h4>
        <?php foreach ($successful as $table => $name) { ?>
            <hr>
            <p><i class="nav-icon icon-check"></i> <?php echo $name; ?> => <?php echo $table; ?></p>
        <?php } ?>
    </div>
<?php } ?>

<?php if($unsuccessful){ ?>
    <div class="alert alert-danger mt-4" role="alert">
        <h4 class="alert-heading"><?php echo LANG_CP_NS_UNSUCCESSFUL; ?></h4>
        <hr>
        <?php $this->renderForm($form, [], array(
            'method' => 'post',
            'submit' => array(
                'title' => LANG_CP_NS_FIX
            )
        ), []); ?>
    </div>
<?php } ?>