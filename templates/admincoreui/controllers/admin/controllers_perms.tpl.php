<?php

    $this->addBreadcrumb(LANG_PERMISSIONS);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

?>
<div id="<?php echo $this->controller->name; ?>_permissions_form">
<?php
    $submit_url = $this->href_to('perms_save', $subject ? $subject : false);

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);
?>
</div>