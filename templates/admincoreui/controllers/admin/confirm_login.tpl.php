<?php $this->setPageTitle($pagetitle); ?>

<div id="confirm_login" class="alert alert-warning mt-4" role="alert">
    <?php if($title){ ?>
        <h4 class="alert-heading"><?php echo $title; ?></h4>
    <?php }?>
    <?php if($hint){ ?>
        <p class="confirm_login_info">
            <?php echo $hint; ?>
        </p>
    <?php } ?>
    <div class="confirm_login_form">
        <?php
            $this->renderForm($form, array(), array(
                'action' => '',
                'method' => 'post',
                'submit' => array(
                    'title' => LANG_CONTINUE
                )
            ), $errors);
        ?>
    </div>
</div>

<script>
    $(function() {
        $('#wrapper').css('background-color', '#EEE');
        $('#cp_header, #cp_left_sidebar, #cp_footer').addClass('blur_block');
    });
</script>