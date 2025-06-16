<?php if ($refs){ ?>
	<div class="billing-ref-tree">
		<?php
			$last_level = false;
			$iteration = 0;
		?>
		<?php foreach(array_reverse($refs) as $ref){ ?>
			<?php $level = $ref['level']; ?>
			<?php if ($last_level === false || $last_level != $level) { ?>
				<?php $last_level = $level; $iteration++; ?>
				<?php $width = number_format(round(100/pow($scale, $iteration), 2), 2, '.', ''); ?>
				<div class="break-line"></div>
			<?php } ?>
			<div class="ref-node" style="width:<?php echo $width; ?>%">
				<div class="ref-block">
					<a href="<?php echo href_to('users', $ref['id']); ?>"><?php html($ref['nickname']); ?></a>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>