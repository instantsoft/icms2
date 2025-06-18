<?php

    $this->setPageTitle(LANG_BILLING_REFS);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user['nickname'], href_to('users', $user['id']));
	$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to('users', $user['id'], 'balance'));
    $this->addBreadcrumb(LANG_BILLING_REFS);

?>

<h1><?php echo $is_own_profile ? LANG_BILLING_REFS : $user['nickname'] . ': <span>' . LANG_BILLING_REFS .'</span>'; ?></h1>

<?php if ($is_own_profile) { ?>
    <div id="billing-refs">
        <div class="note">
            <?php echo LANG_BILLING_REFS_NOTE; ?>
        </div>
        <div class="link gui-panel">
            <h4><?php echo LANG_BILLING_REFS_LINK; ?></h4>
            <div class="url">
                <a href="<?php echo $ref_url; ?>"><?php echo $ref_url; ?></a>
            </div>
        </div>
        <?php if ($ref_bonus || $ref_levels) { ?>
            <div class="income">
                <p><?php echo LANG_BILLING_REFS_INCOME; ?>:</p>
                <ul>
                    <?php if ($ref_bonus) { ?>
                        <li><?php printf(LANG_BILLING_REFS_INCOME_REG, html_spellcount($ref_bonus, $b_spellcount)); ?></li>
                    <?php } ?>
                    <?php foreach($ref_levels as $level => $percent) {?>
                        <?php if ($ref_mode == 'dep') { ?>
                            <li><?php printf(LANG_BILLING_REFS_INCOME_DEP, $percent['percent'].'%', $level+1); ?></li>
                        <?php } ?>
                        <?php if ($ref_mode == 'all') { ?>
                            <li><?php printf(LANG_BILLING_REFS_INCOME_ALL, $percent['percent'].'%', $level+1); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <div class="legal">
            <p><?php echo LANG_BILLING_REFS_LEGAL; ?></p>
            <?php if ($terms_url){ ?>
                <p><a href="<?php echo $terms_url; ?>"><?php echo LANG_BILLING_REFS_TERMS; ?></a></p>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php if ($refs) { ?>
	<div class="billing-history">
		<?php
			$this->renderChild('refs_history', array(
				'refs' => $refs,
                'is_own_profile' => $is_own_profile,
				'total' => $total,
				'page' => $page,
				'perpage' => $perpage,
				'type' => $type,
				'scale' => $scale
			));
		?>
	</div>
<?php } ?>
