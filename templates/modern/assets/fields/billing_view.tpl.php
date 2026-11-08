<div class="billing-plan d-flex align-items-center">
    <div class="h5 m-0">
        <span class="badge badge-primary ">
            <?php echo $plan['title']; ?>
        </span>
    </div>
    <?php if ($is_owner && $plan['date_until']) { ?>
        <span class="badge badge-<?php if ($plan['is_remind_date_until']) { ?>danger<?php } else { ?>success<?php } ?> ml-3">
            <?php printf(LANG_BILLING_PLAN_UNTIL, html_date_time($plan['date_until'])); ?>
        </span>
    <?php } ?>
</div>