<?php

    $this->addBreadcrumb(LANG_PERMISSIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

?>
<div id="<?php echo $this->controller->name; ?>_permissions_form">
<?php
    $submit_url = $this->href_to('perms_save', $subject ? $subject : false);

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);
?>
</div>