<?php
    $this->setPageTitle(LANG_BILLING_FAIL);
    $this->addBreadcrumb(LANG_BILLING_FAIL);
?>

<div class="alert alert-danger">
    <h1 class="alert-heading"><?php echo LANG_BILLING_FAIL; ?></h1>
    <p><?php echo LANG_BILLING_FAIL_NOTICE; ?></p>
    <hr>
    <a class="btn btn-danger" href="<?php echo $next_url; ?>">
        <?php echo LANG_CONTINUE; ?>
    </a>
</div>