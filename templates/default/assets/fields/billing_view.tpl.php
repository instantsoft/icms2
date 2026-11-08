<div class="billing-plan">
    <span class="badge badge-primary ">
        <?php echo $plan['title']; ?>
    </span>
    <?php if ($is_owner && $plan['date_until']) { ?>
        <small class="badge badge-<?php if ($plan['is_remind_date_until']) { ?>danger<?php } else { ?>success<?php } ?> ml-3">
            <?php printf(LANG_BILLING_PLAN_UNTIL, html_date_time($plan['date_until'])); ?>
        </small>
    <?php } ?>
</div>