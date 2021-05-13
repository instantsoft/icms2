<?php
    $this->setPageTitle(LANG_LOG_IN);
    $this->addBreadcrumb(LANG_LOG_IN);
    $is_ajax = $this->controller->request->isAjax();
?>

<?php if($ajax_page_redirect){ ?>
    <script>window.location.href='<?php echo $this->href_to('login'); ?>';</script>
    <?php return; ?>
<?php } ?>

<?php if(!$is_ajax){ ?>
    <h1><?php echo LANG_PLEASE_LOGIN; ?></h1>
<?php } else { ?>
    <div class="modal_padding">
<?php } ?>
<div class="left_cell">
    <?php
        $this->renderForm($form, $data, array(
            'action' => href_to('auth', 'login'),
            'method' => 'post',
            'cancel' => array(
                'show'  => $is_reg_enabled,
                'title' => LANG_NO_ACCOUNT.' '.LANG_REGISTRATION,
                'href'  => $this->href_to('register').($back_url ? '?back='.$back_url : '')),
            'submit' => array(
                'title' => LANG_LOG_IN
            )
        ), $errors);
    ?>
</div>
<?php if($hooks_html){ ?>
    <div class="center_cell"><?php echo LANG_OR; ?></div>
    <div class="right_cell">
        <h3><?php echo LANG_LOG_IN_OPENID; ?></h3>
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>
<?php if($is_ajax){ ?>
    </div>
<?php } ?>
