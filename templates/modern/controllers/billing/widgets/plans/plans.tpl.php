<?php $this->addTplCSSFromContext('controllers/billing/styles'); ?>
<?php if ($plans_desc) { ?>
    <div class="pricing-desc">
        <?php echo $plans_desc; ?>
    </div>
<?php } ?>
<div class="pricing-wraper">
    <?php foreach ($plans as $p) { ?>
        <div class="card pricing-card<?php if($p['id'] == $default_plan_id) { ?> popular border-primary<?php } ?>">

            <?php if($p['id'] == $default_plan_id && $default_plan_badge) { ?>
                <div class="pricing-popular-badge"><?php echo $default_plan_badge; ?></div>
            <?php } ?>

            <div class="card-body">
                <h3 class="plan-name"><?php echo $p['title']; ?></h3>
                <p class="plan-description"><?php echo $p['description']; ?></p>

                <div class="h1 mb-4 pricing-card-title">
                    <span class="price-amount font-weight-bold"><?php echo $p['price']['amount']; ?> <?php echo $cur_real_symb; ?></span>
                    <small class="price-period text-muted">/ <?php echo $p['price']['int_str']; ?></small>
                </div>

                <ul class="plan-features list-unstyled mb-4">
                <?php foreach ($p['features_list'] as $feature) { ?>
                    <li class="plan-features__item plan-features__<?php echo $feature['name']; ?><?php if ($feature['type'] === 'bool' && !$feature['value']) { ?> text-muted<?php } ?>">
                        <?php
                        $icon = 'check';
                        $icon_class = 'text-success';
                        if ($feature['type'] === 'bool' && !$feature['value']) {
                            $icon = 'times';
                            $icon_class = 'text-danger';
                        }
                        ?>
                        <span class="<?php echo $icon_class; ?>">
                            <?php html_svg_icon('solid', $icon); ?>
                        </span>
                        <span>
                            <?php echo $feature['title']; ?>
                            <?php if ($feature['type'] === 'value' && !is_null($feature['value'])) { ?>
                                <?php echo $feature['value']; ?>
                            <?php } ?>
                        </span>
                    </li>
                <?php } ?>
                </ul>

                <a class="btn btn-block<?php if($p['id'] == $default_plan_id) { ?> btn-primary<?php } else { ?> btn-outline-primary<?php } ?><?php if (!empty($p['buy_link_disable'])) { ?> disabled<?php } ?>" href="<?php echo $p['buy_link']; ?>">
                    <?php echo $p['buy_text']; ?>
                </a>
            </div>
        </div>
    <?php } ?>
</div>