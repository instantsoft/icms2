<?php

    $this->addBreadcrumb(LANG_MANIFESTS_CONTROLLER);

    if (!empty($events_add) || !empty($events_delete)){

        $this->addToolButton(array(
            'class' => 'add',
            'title' => LANG_MANIFESTS_REFRESH_HOOKS,
            'href'  => $this->href_to('update')
        ));

    }

    if (!empty($events_delete)){

?>
        <div class="hooks_delete">
            <div class="hooks_delete_title"><?php echo LANG_MANIFESTS_EVENTS_TO_BE_DELETED;?></div>
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
        <div class="hooks_add">
            <div class="hooks_add_title"><?php echo LANG_MANIFESTS_ALLOW_NEW_EVENTS;?></div>
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

<?php $this->renderGrid($this->href_to('ajax'), $grid); ?>

<div class="buttons">
    <?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('reorder')}')"); ?>
</div>