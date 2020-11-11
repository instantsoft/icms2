<?php if($form_data['title'] && !empty($form_data['options']['show_title'])){ ?>
    <h4><?php echo $form_data['title']; ?></h4>
<?php } ?>
<?php if($form_data['description']){?>
    <?php echo $form_data['description']; ?>
<?php } ?>
<?php $this->renderForm($form, [], $form_data['params'], false); ?>
<div id="after-submit" class="d-none icms-forms__full-msg">
    <div class="success-text"></div>
    <?php if(!empty($form_data['options']['continue_link'])){ ?>
        <a href="<?php echo $form_data['options']['continue_link']; ?>"><?php echo LANG_CONTINUE; ?></a>
    <?php } ?>
</div>
<?php ob_start(); ?>
<script>
    function formsSuccess (form_data, result){
        $('#after-submit').toggleClass('d-flex d-none');
        $('#after-submit > .success-text').html(result.success_text);
    }
</script>
<?php $this->addBottom(ob_get_clean()); ?>