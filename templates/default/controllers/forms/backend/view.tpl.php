<?php if($form_data['title']){?>
    <h3><?php echo $form_data['title']; ?></h3>
<?php } ?>
<?php if($form_data['description']){?>
    <?php echo $form_data['description']; ?>
<?php } ?>
<?php
    $this->renderForm($form, [], [
        'action' => '',
        'submit' => ['title' => LANG_SEND],
        'method' => 'post'
    ], $errors);
