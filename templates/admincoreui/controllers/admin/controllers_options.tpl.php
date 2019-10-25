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

?>
<div id="<?php echo $this->controller->name; ?>_options_form">
<?php
    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
</div>