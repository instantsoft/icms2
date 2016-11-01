<?php

    $this->addBreadcrumb(LANG_OPTIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    if($toolbar){
        foreach ($toolbar as $menu) {
            $this->addToolButton($menu);
        }
    }

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
<div id="<?php echo $this->controller->name; ?>_options_form">
<?php
    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
</div>