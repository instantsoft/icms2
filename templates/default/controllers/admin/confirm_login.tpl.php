<?php $this->setPageTitle($pagetitle); ?>

<h1><?php echo $pagetitle; ?></h1>

<div id="confirm_login">
    <?php if($title){ ?>
        <h2><?php echo $title; ?></h2>
    <?php }?>
    <div class="confirm_login_wrap">
        <?php if($hint){ ?>
            <div class="confirm_login_info">
                <?php echo $hint; ?>
            </div>
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
</div>

<script>
    $(function() {
        $('#wrapper').css('background-color', '#EEE');
        $('#cp_header, #cp_top_line, #cp_footer').addClass('blur_block');
    });
</script>