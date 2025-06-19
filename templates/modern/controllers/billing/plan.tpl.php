<?php
    $this->setPageTitle($title);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb($title);

    $this->addTplJSName('billing');
?>

<h1><?php echo $title; ?></h1>

<?php if (!$plans) { ?>
    <div class="alert alert-info mt-3 mt-lg-4">
        <p><?php echo LANG_BILLING_PLANS_NONE; ?></p>
        <a class="btn btn-primary" href="<?php echo href_to_profile($user, ['balance']); ?>">
            <?php echo LANG_BACK; ?>
        </a>
    </div>
<?php return; } ?>

<div class="billing-plan row mt-3 mt-lg-4">
    <div class="billing-plan-form col-sm-8">
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <?php echo html_csrf_token(); ?>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_PLAN; ?></label>
                        <div class="col-sm-8">
                            <?php if ($is_renew_plan) { ?>
                                <div class="form-control-plaintext">
                                    <?php echo $current_plan['title']; ?>
                                </div>
                            <?php } ?>
                            <?php echo html_select('plan_id', $plans_list, $selected_plan, ['id' => 'plan_id', 'class' => $is_renew_plan?'d-none':'']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_PLAN_DESC; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php foreach ($plans as $p) { ?>
                                    <div class="plan-desc" id="plan-desc-<?php echo $p['id']; ?>">
                                        <?php echo $p['description']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_PLAN_LENGTH; ?></label>
                        <div class="col-sm-8" id="plan-len">
                            <?php foreach ($plans as $p) { ?>
                                <div class="plan-len" id="plan-len-<?php echo $p['id']; ?>">
                                    <?php foreach ($p['prices'] as $idx => $price) { ?>
                                        <?php $price_hint = $p['is_real_price'] ? $price['price'] . ' ' . $curr : html_spellcount(round($price['amount'], 2), $b_spellcount); ?>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" value="<?php echo $idx; ?>" id="len<?php echo $p['id']; ?>-<?php echo $idx; ?>" name="len<?php echo $p['id']; ?>" class="custom-control-input" data-price="<?php html($price_hint); ?>">
                                            <label class="custom-control-label" for="len<?php echo $p['id']; ?>-<?php echo $idx; ?>">
                                                <?php echo $price['spellcount']; ?>
                                                <?php if (!empty($price['cashback']) && $price['cashback']) { ?>
                                                    (+<?php echo html_spellcount($price['cashback'], $b_spellcount); ?>)
                                                <?php } ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_PLAN_PRICE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext" id="plan-price"></div>
                        </div>
                    </div>

                    <div class="form-group row plan-system-select">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?></label>
                        <div class="col-sm-8">
                            <?php echo html_select('system', $systems_list); ?>
                        </div>
                    </div>

                    <div class="buttons mt-3 mt-lg-4">
                        <?php echo html_submit(LANG_BILLING_BUY); ?>
                        <?php if ($is_renew_plan && count($plans) > 1) { ?>
                            <a class="btn btn-outline-primary ml-2" href="<?php echo $this->href_to('plan') ?>"><?php echo LANG_BILLING_CHANGE_PLAN; ?></a>
                        <?php } ?>
                        <a class="back-btn btn btn-link ml-2" href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_CANCEL; ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
	$(function() {
        icms.billing.initPlanSelect(<?php echo json_encode($real_price_plans); ?>);
	});
</script>
<?php $this->addBottom(ob_get_clean()); ?>