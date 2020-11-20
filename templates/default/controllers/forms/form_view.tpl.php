<?php
    $this->addTplJSName([
        'forms-constructor'
    ]);
?>
<div class="icms-forms__wrap">
    <?php if($form_data['title'] && !empty($form_data['options']['show_title'])){ ?>
        <h3><?php echo $form_data['title']; ?></h3>
    <?php } ?>
    <?php if($form_data['description']){?>
        <?php echo $form_data['description']; ?>
    <?php } ?>
    <?php $this->renderForm($form, [], $form_data['params'], false); ?>
</div>