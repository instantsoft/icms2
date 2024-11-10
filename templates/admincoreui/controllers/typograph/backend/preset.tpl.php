<?php

    $this->addTplJSName([
        'admin-typograph'
    ]);

    $this->addBreadcrumb(LANG_TYP_PRESETS, $this->href_to(''));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($preset['title']);
    }

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to(''),
        'icon'  => 'undo'
    ]);
?>
<div id="form-preset" data-options_url="<?php echo $this->href_to('tags_options'); ?>" data-preset_id="<?php echo $preset['id'] ?? 0; ?>">
<?php
    $this->renderForm($form, $preset, [
        'action' => '',
        'method' => 'post'
    ], $errors);
?>
</div>