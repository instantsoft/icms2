<?php

    $this->addBreadcrumb(LANG_PERMISSIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $help_href_const = 'LANG_HELP_URL_COM_'.strtoupper($this->controller->name);
    if(defined($help_href_const)){
        $this->addToolButton(array(
            'class'  => 'help',
            'title'  => LANG_HELP,
            'target' => '_blank',
            'href'   => constant($help_href_const)
        ));
    }

?>
<div id="<?php echo $this->controller->name; ?>_permissions_form">
<?php
    $submit_url = $this->href_to('perms_save', $subject ? $subject : false);

    echo $this->renderPermissionsGrid($rules, $groups, $values, $submit_url);
?>
</div>