<?php

    $this->addBreadcrumb(LANG_OPTIONS);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    if($toolbar){
        foreach ($toolbar as $menu) {
            $this->addToolButton($menu);
        }
    }

?>
<div id="<?php echo $this->controller->name; ?>_options_form">
<?php
    $this->renderForm($form, $options, [
        'action' => '',
        'method' => 'post'
    ], $errors);
?>
</div>