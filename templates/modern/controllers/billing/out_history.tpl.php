<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-3 mb-lg-4"><?php echo LANG_BILLING_OUT_HISTORY; ?></h4>
        <table class="billing-log table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo LANG_BILLING_LOG_DATE; ?></th>
                    <th><?php echo LANG_BILLING_OUT_SYSTEM; ?></th>
                    <th><?php echo LANG_BILLING_LOG_AMOUNT; ?></th>
                    <th><?php echo LANG_BILLING_OUT_STATUS; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($outs as $out){ ?>
                <tr>
                    <td><?php echo $out['id']; ?></td>
                    <td><?php echo html_date_time($out['date_created']); ?></td>
                    <td>
                        <?php html($out['system'] . ': ' . $out['purse']); ?>
                    </td>
                    <td class="col-summ"><?php echo $out['summ'] ? $out['summ'] . ' ' . $currency_real : '&mdash;'; ?></td>
                    <td>
                        <span class="status-<?php echo $out['status']; ?>">
                            <?php echo $out['status_text']; ?>
                            <?php if ($out['show_date_done']) { echo ' - '. html_date_time($out['date_done']); } ?>
                        </span>
                        <?php if ($out['can_delete']) { ?>
                            <a class="ml-3 text-danger" href="<?php echo $this->href_to('out_delete', $out['id'], ['csrf_token' => cmsForm::getCSRFToken()]); ?>">
                                <?php echo LANG_CANCEL; ?>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if ($total > $perpage) { ?>
            <?php echo html_pagebar($page, $perpage, $total); ?>
        <?php } ?>
    </div>
</div>