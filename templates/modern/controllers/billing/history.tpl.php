<div class="card mt-3 mt-lg-4">
    <div class="card-body">
        <h4 class="card-title mb-4"><?php echo LANG_BILLING_LOG_HISTORY; ?></h4>

        <?php if (!$total) { ?>
            <div class="alert alert-info">
                <?php echo LANG_BILLING_LOG_EMPTY; ?>
            </div>
        <?php } ?>

        <?php if ($total) { ?>
            <div class="table-responsive">
                <table class="billing-log table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?php echo LANG_BILLING_LOG_DATE; ?></th>
                            <th><?php echo LANG_BILLING_LOG_DESCRIPTION; ?></th>
                            <th><?php echo LANG_BILLING_LOG_AMOUNT; ?></th>
                            <th><?php echo LANG_BILLING_LOG_SUMM; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operations as $op){ ?>
                        <tr>
                            <td><?php echo html_date_time($op['date_done']); ?></td>
                            <td>
                                <?php
                                    if ($op['url']) {
                                        html_link($op['description'], rel_to_href($op['url']));
                                    } else {
                                        html($op['description']);
                                    }
                                ?>
                            </td>
                            <td>
                                <span class="<?php echo html_signed_class($op['amount']); ?>">
                                    <?php echo html_signed_num(nf($op['amount'], 2)); ?>
                                </span>
                            </td>
                            <td><?php echo $op['summ'] ? nf($op['summ']) . ' ' . $currency_real : '&mdash;'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php echo html_pagebar($page, $perpage, $total); ?>
        <?php } ?>
    </div>
</div>