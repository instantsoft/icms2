<?php

    $this->setPageTitle(LANG_BILLING_BUY_PLAN);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_BUY_PLAN);
?>

<h1><?php echo LANG_BILLING_BUY_PLAN; ?></h1>

<?php if (!$plans) { ?>
	<div class="billing-transfer">
		<div class="error"><?php echo LANG_BILLING_PLANS_NONE; ?></div>
		<p><a href="<?php echo href_to('users', $user->id, 'balance'); ?>"><?php echo LANG_BACK; ?></a></p>
	</div>
<?php return; } ?>


<div class="billing-plan">

	<div class="billing-plan-form">
		<form action="" method="post">
			<table>
				<tbody>
					<tr>
						<td class="title"><?php echo LANG_BILLING_PLAN; ?>:</td>
						<td>
							<?php echo html_select('plan_id', array_collection_to_list($plans, 'id', 'title'), $selected_plan); ?>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_PLAN_DESC; ?>:</td>
						<td>
							<?php foreach($plans as $p) { ?>
								<div class="plan-desc plan-desc-<?php echo $p['id']; ?>"><?php echo $p['description'] ;?></div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_PLAN_LENGTH; ?>:</td>
						<td>
							<?php foreach($plans as $p) { ?>
								<div class="plan-len plan-len-<?php echo $p['id']; ?>">
									<?php foreach($p['prices'] as $idx=>$price) { ?>
                                    <?php $price_hint = $p['is_real_price'] ? $price['price'] . ' ' . $curr : html_spellcount(round($price['amount'], 2), $b_spellcount); ?>
										<label>
    										<?php echo html_radio("len{$p['id']}", false, $idx, array('data-price' => $price_hint)); ?>
											<?php echo $price['spellcount']; ?>
											<?php if (!empty($price['cashback']) && floatval($price['cashback'])) { ?>
												(+<?php echo html_spellcount(floatval($price['cashback']), $b_spellcount); ?>)
											<?php } ?>
										</label>
									<?php } ?>
								</div>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_PLAN_PRICE; ?>:</td>
						<td>
							<div class="plan-price"></div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="plan-system-select">
				<table>
					<tbody>
						<tr>
							<td class="title"><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?>:</td>
							<td>
								<?php echo html_select('system', $systems_list); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<input type="submit" class="button-submit" name="submit" value="<?php echo LANG_BILLING_BUY; ?>">
			<a class="back-btn" href="<?php echo href_to('users', $user->id, 'balance'); ?>"><?php echo LANG_CANCEL; ?></a>
		</form>
	</div>

</div>

<script>
	$(document).ready(function(){

		$('.billing-plan-form select').eq(0).on('change', function(){
			updatePlanInfo();
		});

		$('.billing-plan-form .plan-len input:radio').on('click', function(){
			$('.billing-plan-form .plan-price').html($(this).data('price'));
		});

		updatePlanInfo();

	});

	function updatePlanInfo(){

		var real_price_plans = <?php echo json_encode($real_price_plans); ?>;
		var plan_id = $('.billing-plan-form select').val();

		$('.plan-desc').hide(); $('.plan-desc-'+plan_id).show();
		$('.plan-len').hide(); $('.plan-len-'+plan_id).show();

		$('.plan-len-'+plan_id+' input:radio').eq(0).trigger('click');

		$('.plan-system-select').toggle(real_price_plans.indexOf(plan_id)>=0);

	}

</script>
