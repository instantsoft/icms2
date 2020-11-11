<?php
    $this->setPageTitle($form_data['title']);
    $this->addBreadcrumb($form_data['title']);
?>
<h1><?php echo $form_data['title']; ?></h1>
<?php if($form_data['description']){?>
    <?php echo $form_data['description']; ?>
<?php } ?>
<?php $this->renderForm($form, [], $form_data['params'], $errors); ?>
<div id="after-submit" class="d-none alert alert-dismissible flex-column align-items-center justify-content-center w-100 h-100 position-absolute icms-forms__full-msg">
    <div class="success-text display-4 text-center"></div>
    <?php if(!empty($form_data['options']['continue_link'])){ ?>
        <a href="<?php echo $form_data['options']['continue_link']; ?>" class="mt-3 btn btn-success"><?php echo LANG_CONTINUE; ?></a>
    <?php } ?>
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php ob_start(); ?>
<script>
    function formsSuccess (form_data, result){
        $('#after-submit').toggleClass('d-flex d-none');
        $('#after-submit > .success-text').html(result.success_text);
    }
</script>
<?php $this->addBottom(ob_get_clean()); ?>