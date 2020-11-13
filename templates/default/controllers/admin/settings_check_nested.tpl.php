<h1><?php echo LANG_CP_SECTION_SETTINGS; ?></h1>
<?php

$this->setPageTitle(LANG_CP_CHECK_NESTED);

$this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
$this->addBreadcrumb(LANG_CP_CHECK_NESTED);

$this->addMenuItems('settings', $this->controller->getSettingsMenu());

?>

<div class="pills-menu">
    <?php $this->menu('settings', true, 'nav-pills'); ?>
</div>

<?php if(!$nested_tables_exists){ ?>
    <div class="sess_messages" style="margin-top: 20px;">
        <div class="message_info"><?php echo LANG_CP_NS_NO_TABLES; ?></div>
    </div>
<?php return; } ?>

<?php if($successful){ ?>
    <div class="alert alert-success mt-4" role="alert">
        <h3 class="alert-heading"><?php echo LANG_CP_NS_SUCCESSFUL; ?></h3>
        <?php foreach ($successful as $table => $name) { ?>
            <hr>
            <p><i class="nav-icon icon-check"></i> <?php echo $name; ?> => <?php echo $table; ?></p>
        <?php } ?>
    </div>
<?php } ?>

<?php if($unsuccessful){ ?>
    <div class="alert alert-danger mt-4" style="margin-top: 20px;">
        <h3 class="alert-heading"><?php echo LANG_CP_NS_UNSUCCESSFUL; ?></h3>
        <?php $this->renderForm($form, [], array(
            'method' => 'post',
            'submit' => array(
                'title' => LANG_CP_NS_FIX
            )
        ), []); ?>
    </div>
<?php } ?>