<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);
?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE; ?></h1>

<div id="cp_package_ftp_notices">
    <div class="notice">
        <?php echo LANG_CP_INSTALL_FTP_NOTICE; ?>
        <?php echo LANG_CP_INSTALL_FTP_PRIVACY; ?>
    </div>
</div>

<?php
    $this->renderForm($form, $account, array(
        'action' => '',
        'method' => 'post',
        'submit' => array(
            'title' => LANG_CONTINUE
        ),
        'cancel' => array(
            'show' => true,
            'href' => $this->href_to('')
        )
    ), $errors); ?>

<?php echo html_button(LANG_INSTALL, 'skip', "location.href='{$this->href_to('install/finish')}'", array('style'=>'display: none;','id'=>'skip')); ?>

<script type="text/javascript">
    $(function() {
        $('form > .buttons').prepend($('#skip'));
        $('#is_skip').on('click', function (){
            form = $(this).parents('form');
            if($(this).is(':checked')){
                $(form).find('input').not(this).not('.buttons > input').prop('disabled', true);
                $(form).find('.button-submit').hide();
                $('#skip').show();
            } else {
                $(form).find('input').prop('disabled', false);
                $(form).find('.button-submit').show();
                $('#skip').hide();
            }
        });
    });
</script>