<?php
    $this->setPageTitle($form_data['title']);
    $this->addBreadcrumb($form_data['title']);
    $this->addTplJSNameFromContext([
        'forms-constructor'
    ]);
?>
<h1><?php echo $form_data['title']; ?></h1>
<?php if($form_data['description']){?>
    <?php echo $form_data['description']; ?>
<?php } ?>
<div class="position-relative icms-forms__wrap">
    <?php $this->renderForm($form, [], $form_data['params'], false); ?>
</div>
