<?php

    $this->setPageTitle(LANG_EVENTS_MANAGEMENT);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->href_to('controllers'));

    $this->addBreadcrumb(LANG_EVENTS_MANAGEMENT);


    if (!empty($events_add) || !empty($events_delete)){

        $this->addToolButton(array(
            'class' => 'refresh',
            'title' => LANG_EVENTS_REFRESH,
            'href'  => $this->href_to('controllers', array('events_update'))
        ));

    }

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_EVENTS
	));

?>

<h1><?php echo LANG_EVENTS_MANAGEMENT; ?></h1>

<?php
    if (!empty($events_delete)){

?>
        <div class="events_delete">
            <div class="events_delete_title"><?php echo LANG_EVENTS_DELETED;?></div>
<?php
        foreach ($events_delete as $controller => $events){

?>
            <div class="controller_name"><?php echo string_lang($controller.'_CONTROLLER', $controller);?></div>
<?php
            foreach ($events as $event_name){
?>
                <div class="event_name"><?php echo $event_name;?></div>
<?php
            }

        }
?>
        </div>
<?php
    }

    if (!empty($events_add)){

?>
        <div class="events_add">
            <div class="events_add_title"><?php echo LANG_EVENTS_ALLOW_NEW;?></div>
<?php
        foreach ($events_add as $controller => $events){

?>
            <div class="controller_name"><?php echo string_lang($controller.'_CONTROLLER', $controller);?></div>
<?php
            foreach ($events as $event_name){
?>
                <div class="event_name"><?php echo $event_name;?></div>
<?php
            }

        }
?>
        </div>
<?php

    }

?>

<?php echo $grid_html; ?>