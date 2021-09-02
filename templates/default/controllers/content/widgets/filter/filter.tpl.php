<div class="widget_content_filter">
	<div class="filter-container">
		<form action="<?php echo $page_url; ?>" method="get" id="<?php echo $form_id; ?>">
			<?php echo html_input('hidden', 'page', 1); ?>
			<div class="fields">
				<?php foreach($fields as $name => $field){ ?>
					<?php $value = isset($filters[$name]) ? $filters[$name] : null; ?>
					<?php $output = $field['handler']->getFilterInput($value); ?>
					<?php if (!$output){ continue; } ?>
					<div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
						<div class="title"><?php echo $field['title']; ?></div>
						<div class="value">
							<?php echo $output; ?>
						</div>
					</div>
				<?php } ?>
				<?php if (!empty($props)){ ?>
					<?php foreach($props as $prop){ ?>
						<?php $value = isset($filters["p{$prop['id']}"]) ? $filters["p{$prop['id']}"] : null; ?>
                        <?php $output = $prop['handler']->getFilterInput($value); ?>
                        <?php if (!$output){ continue; } ?>
						<div class="field ft_<?php echo $prop['type']; ?> f_prop_<?php echo $prop['id']; ?>">
							<div class="title"><?php echo $prop['title']; ?></div>
							<div class="value">
								<?php echo $output; ?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="buttons">
				<?php echo html_submit(LANG_FILTER_APPLY); ?>
				<?php if (count($filters)){ ?>
                    <a class="cancel_filter_link" href="<?php echo $page_url; ?>"><?php echo LANG_CANCEL; ?></a>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
<?php ob_start(); ?>
<script>
    $(function (){
        icms.forms.initFilterForm('#<?php echo $form_id; ?>');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>