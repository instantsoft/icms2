<div class="card mt-3 mt-lg-4">
    <div class="card-body">
        <?php if ($is_own_profile) { ?>
            <h4 class="card-title"><?php printf(LANG_BILLING_REFS_HISTORY, $total); ?></h4>
        <?php } ?>
        <?php
        if ($type === 'collect') {
            $this->renderChild('refs_tree', [
                'refs'  => $refs,
                'scale' => $scale
            ]);
        }
        ?>
        <div class="table-responsive mt-3">
            <table class="billing-log table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo LANG_BILLING_REFS_HISTORY_DATE; ?></th>
                        <th><?php echo LANG_BILLING_REFS_HISTORY_USER; ?></th>
                        <th><?php echo LANG_BILLING_REFS_HISTORY_LEVEL; ?></th>
                        <th><?php echo LANG_BILLING_REFS_HISTORY_INCOME_30; ?></th>
                        <th><?php echo LANG_BILLING_REFS_HISTORY_INCOME; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($refs as $ref) { ?>
                        <tr>
                            <td><?php echo html_date_time($ref['date']); ?></td>
                            <td>
                                <a href="<?php echo href_to_profile($ref); ?>">
                                    <?php html($ref['nickname']); ?>
                                </a>
                            </td>
                            <td><?php echo $ref['level']; ?></td>
                            <td>
                                <span class="<?php echo html_signed_class($ref['income_month']); ?>">
                                    <?php echo $ref['income_month'] ?: 0; ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?php echo html_signed_class($ref['income_total']); ?>">
                                    <?php echo $ref['income_total'] ?: 0; ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo html_pagebar($page, $perpage, $total); ?>
    </div>
</div>