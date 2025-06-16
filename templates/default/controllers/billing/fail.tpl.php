<?php
    $this->setPageTitle(LANG_BILLING_FAIL);
    $this->addBreadcrumb(LANG_BILLING_FAIL);
?>

<h1><?php echo LANG_BILLING_FAIL; ?></h1>

<div class="billing-result-page">

	<div class="notice"><?php echo LANG_BILLING_FAIL_NOTICE; ?></div>
	<a class="continue" href="<?php echo $next_url; ?>"><?php echo LANG_CONTINUE; ?></a>

</div>