<?php

    $this->setPageTitle(LANG_BILLING_REFS);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user['nickname'], href_to_profile($user));
	$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_REFS);

?>

<h1><?php echo $is_own_profile ? LANG_BILLING_REFS : $user['nickname'] . ': <span>' . LANG_BILLING_REFS .'</span>'; ?></h1>

<?php if ($is_own_profile) { ?>
    <div id="billing-refs" class="mt-3 mt-lg-4">
        <div class="alert alert-info">
            <?php echo LANG_BILLING_REFS_NOTE; ?>
            <p class="mb-0 mt-3"><?php echo LANG_BILLING_REFS_LEGAL; ?></p>
            <?php if ($terms_url) { ?>
                <p class="mb-0 mt-2"><a href="<?php echo $terms_url; ?>"><?php echo LANG_BILLING_REFS_TERMS; ?></a></p>
            <?php } ?>
        </div>
        <div class="card mt-3 mt-lg-4">
            <div class="card-body">
                <h4 class="card-title"><?php echo LANG_BILLING_REFS_LINK; ?></h4>
                <div class="card-text">
                    <?php echo html_input('text', 'ref', $ref_url, ['readonly' => true, 'class' => 'icms-click-select']); ?>
                </div>
                <?php if ($ref_bonus || $ref_levels) { ?>
                    <h4 class="card-title mt-3 mt-lg-4"><?php echo LANG_BILLING_REFS_INCOME; ?></h4>
                    <div class="alert alert-success mb-0">
                        <ul class="mb-0">
                            <?php if ($ref_bonus) { ?>
                                <li><?php printf(LANG_BILLING_REFS_INCOME_REG, html_spellcount($ref_bonus, $b_spellcount)); ?>;</li>
                            <?php } ?>
                            <?php foreach ($ref_levels as $level => $percent) { ?>
                                <?php if ($ref_mode == 'dep') { ?>
                                    <li><?php printf(LANG_BILLING_REFS_INCOME_DEP, $percent['percent'] . '%', $level + 1); ?>;</li>
                                <?php } ?>
                                <?php if ($ref_mode == 'all') { ?>
                                    <li><?php printf(LANG_BILLING_REFS_INCOME_ALL, $percent['percent'] . '%', $level + 1); ?>;</li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($refs) { ?>
	<div class="billing-history">
		<?php
			$this->renderChild('refs_history', [
				'refs'           => $refs,
                'is_own_profile' => $is_own_profile,
                'total'          => $total,
                'page'           => $page,
                'perpage'        => $perpage,
                'type'           => $type,
                'scale'          => $scale
            ]);
		?>
	</div>
<?php } ?>