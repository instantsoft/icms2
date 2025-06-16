<?php

	function n($num){
		if (!$num) { return 0; }
		$n = nf($num, 2, ' ');
		$n = explode('.', $n);
		return $n[0] . (!empty($n[1]) ? "<small>.{$n[1]}</small>" : '');
	}

	$profit_periods = [LANG_TODAY, LANG_YESTERDAY, LANG_WEEK, LANG_MONTH];
	$users_tops = [LANG_BILLING_CP_DB_TOP10_DESC, LANG_BILLING_CP_DB_TOP10_ASC];

?>

<div id="billing-dashboard" class="card billing-dashboard card-accent-primary">
    <div class="card-body p-lg-4">
        <div class="row">
            <div class="col-sm-5">
                <div class="row no-gutters mb-3 mb-lg-4">
                    <div class="col-sm-6">
                        <div class="callout callout-success m-0">
                            <div class="h4"><?php echo LANG_BILLING_CP_DB_TOTAL; ?></div>
                            <h4 class="h2"><?php echo n($total); ?> <small><?php echo html_spellcount_only($total, $options['currency']); ?></small></h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="callout callout-danger m-0">
                            <div class="h4"><?php echo LANG_BILLING_CP_DB_DEBT; ?></div>
                            <h4 class="h2"><?php echo n($debt); ?> <small><?php echo $options['cur_real_symb']; ?></small></h4>
                        </div>
                    </div>
                </div>
                <div class="mb-3 mb-lg-4">
                    <h5 class="card-title"><?php echo LANG_BILLING_CP_DB_PROFIT. ', ' . $options['currency_real']; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <?php foreach($profit_periods as $id => $title) { ?>
                                    <tr>
                                        <td width="120"><?php echo $title; ?></td>
                                        <td class="font-weight-bold">
                                            <?php echo n($profit[$id]); ?>
                                            <small><?php echo $options['cur_real_symb']; ?></small>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h5 class="card-title"><?php echo LANG_BILLING_CP_DB_PLANS; ?></h5>
                    <?php if (!$plans){ ?>
                        <div class="alert alert-secondary"><?php echo LANG_BILLING_CP_DB_PLANS_NONE; ?></div>
                    <?php } ?>
                    <?php if ($plans){ ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <tbody>
                                    <?php foreach($plans as $plan) { ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $this->href_to('plans', ['edit', $plan['id']]); ?>">
                                                    <?php echo $plan['title']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($plan['users']) { ?>
                                                    <a href="<?php echo $this->href_to('plans', ['users', $plan['id']]); ?>" class="text-decoration-none d-flex align-items-center">
                                                        <b class="mr-2"><?php echo $plan['users']; ?></b>
                                                        <?php html_svg_icon('solid', 'arrow-right'); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <b><?php echo $plan['users']; ?></b>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php foreach($users_tops as $id => $title) { ?>
                <div class="col-sm-auto">
                    <h5 class="card-title mt-3 mt-lg-0"><?php echo $title; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datagrid mb-0">
                            <tbody>
                                <?php foreach($users[$id] as $user) { ?>
                                    <tr>
                                        <td class="px-3">
                                            <a class="d-flex align-items-center" href="<?php echo href_to_profile($user, ['balance']); ?>">
                                                <span class="icms-user-avatar mr-2 <?php if ($user['is_online']) { ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                                                    <?php echo html_avatar_image($user['avatar'], 'micro', $user['nickname']); ?>
                                                </span>
                                                <?php echo $user['nickname']; ?>
                                            </a>
                                        </td>
                                        <td class="px-4">
                                            <b><?php echo n($user['balance']); ?></b>
                                             <small><?php echo html_spellcount_only($user['balance'], $options['currency']); ?></small>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>