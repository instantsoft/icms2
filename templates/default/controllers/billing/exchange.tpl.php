<?php

$this->setPageTitle(LANG_BILLING_EXCHANGE);

$this->addBreadcrumb(LANG_USERS, href_to('users'));
$this->addBreadcrumb($user->nickname, href_to_profile($user));
$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
$this->addBreadcrumb(LANG_BILLING_EXCHANGE);

?>

<h1><?php echo LANG_BILLING_EXCHANGE; ?></h1>

<?php if (!$is_can_exchange) { ?>
	<div class="billing-transfer">
		<div class="error"><?php echo LANG_BILLING_EXCHANGE_NONE; ?></div>
		<p><a href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_BACK; ?></a></p>
	</div>
<?php return; } ?>

<div class="billing-transfer">

	<div class="billing-transfer-form">
		<form action="" method="post">
            <?php echo html_csrf_token(); ?>
			<table>
				<tbody>
					<tr>
						<td class="title"><?php echo LANG_BILLING_EXCHANGE_MODE; ?>:</td>
						<td>
							<?php echo html_select('mode', $modes, false, ['id' => 'mode']); ?>
						</td>
					</tr>
					<tr>
						<td class="title">
							<span class="rtp"><?php echo LANG_BILLING_EXCHANGE_R; ?>:</span>
							<span class="ptr"><?php echo LANG_BILLING_EXCHANGE_P; ?>:</span>
						</td>
						<td>
							<span class="rtp"><?php echo $user->rating; ?></span>
							<span class="ptr"><?php echo html_spellcount($balance, $b_spellcount); ?></span>
						</td>
					</tr>
					<tr>
						<td class="title">
							<?php echo LANG_BILLING_EXCHANGE_RATE; ?>:
						</td>
						<td>
							1 &rarr;
							<span class="rtp"><?php echo $rtp_rate; ?></span>
							<span class="ptr"><?php echo $ptr_rate; ?></span>
						</td>
					</tr>
					<tr>
						<td class="title">
							<?php echo LANG_BILLING_EXCHANGE_OUT; ?>:
						</td>
						<td>
							<?php echo html_input('text', 'amount', 0, array('id'=>'amount', 'class'=>'input-number', 'autocomplete'=>"off")); ?>
							<span class="rtp"><?php echo LANG_BILLING_EXCHANGE_RATING_UNITS; ?></span>
							<span class="ptr"><?php echo $b_spellcount_arr[2]; ?></span>
						</td>
					</tr>
					<tr>
						<td class="title">
							<?php echo LANG_BILLING_EXCHANGE_IN; ?>:
						</td>
						<td>
							<div class="result-info">
								<span id="exc-result">0</span>
								<span class="rtp"><?php echo $b_spellcount_arr[2]; ?></span>
								<span class="ptr"><?php echo LANG_BILLING_EXCHANGE_RATING_UNITS; ?></span>
							</div>
							<div class="error max-error">
								<?php echo LANG_BILLING_EXCHANGE_MAX; ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="submit" name="submit" class="button-submit" value="<?php echo LANG_BILLING_EXCHANGE_SUBMIT; ?>">
			<a class="back-btn" href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_CANCEL; ?></a>
		</form>
	</div>

</div>

<script>
	$(document).ready(function(){
        var rates = {'rtp': <?php echo $rtp_rate; ?>, 'ptr': <?php echo $ptr_rate; ?>};
        var maxes = {'rtp': <?php echo $user->rating; ?>, 'ptr': <?php echo $balance; ?>};
		var mode = false;
		$('input#amount').on('keyup', function(){
			var amount = Number($(this).val().replace(',', '.'));
			if (!amount) { $('#exc-result').html('0'); return; }
			if (amount > maxes[mode] || amount<0) { $('.max-error').show(); $('.result-info').hide(); return; }
			$('.max-error').hide();
			$('.result-info').show();
			var rate = rates[mode];
			var result = Math.round(amount * rate * 100)/100;
			$('#exc-result').html(result);
		});
		$('select#mode').on('change', function(){
			mode = $(this).val();
			if (mode=='rtp'){
				$('span.rtp').show(); $('span.ptr').hide();
			} else {
				$('span.rtp').hide(); $('span.ptr').show();
			}
			$('input#amount').val(maxes[mode]).trigger('keyup');
		});
		$('select#mode').trigger('change');
	});
</script>
