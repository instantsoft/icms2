<div class="d-flex align-items-center justify-content-between mb-3">
<?php if($form_data['title']){?>
    <h3 class="m-0"><?php echo $form_data['title']; ?></h3>
<?php } ?>
<?php if(!empty($form_data['options']['available_by_link'])){?>
    <a href="<?php echo href_to('forms', $form_data['name']); ?>" target="_blank" class="btn btn-primary"><?php echo LANG_VIEW; ?></a>
<?php } ?>
</div>
<?php if($form_data['description']){?>
    <?php echo $form_data['description']; ?>
<?php } ?>
<?php
    $this->renderForm($form, [], $form_data['params'], $errors);
