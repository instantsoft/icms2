<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE.' «'.$manifest['info']['title'].'»');
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE, $this->href_to('install'));
    $this->addBreadcrumb($manifest['info']['title']);

	$this->addToolButton(array(
		'class'  => 'help',
        'title'  => LANG_HELP,
        'target' => '_blank',
        'href'   => LANG_HELP_URL_INSTALL
    ));

    $this->addToolButton(array(
        'class' => 'addons',
        'title' => LANG_CP_OFICIAL_ADDONS,
        'href'  => $this->href_to('addons_list')
    ));

    if(!empty($manifest['notice_system_files'])){
        cmsUser::addSessionMessage($manifest['notice_system_files'], 'error');
    }

?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE.' «'.$manifest['info']['title'].'»'; ?></h1>

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
            'href' => $this->href_to('addons_list')
        )
    ), $errors); ?>

<?php echo html_button(LANG_INSTALL, 'skip', '', array('style'=>'display: none;','id'=>'skip')); ?>

<script>
    $(function() {
        $('form > .buttons').prepend($('#skip'));
        $('#is_skip').on('click', function (){
            icms.forms.submitted = true;
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
        $('#skip').on('click', function (){
            location.href='<?php echo $this->href_to('install/finish'); ?>';
            return false;
        });
        $('#check_ftp').on('click', function (){
            icms.modal.openAjax($(this).attr('href'), {host: $('#host').val(), port: $('#port').val(), user: $('#user').val(), pass: $('#pass').val(), path: $('#path').val(), is_pasv: $('#is_pasv').val()}, false, '<?php echo LANG_CP_FTP_CHECK; ?>');
            return false;
        });
    });
</script>