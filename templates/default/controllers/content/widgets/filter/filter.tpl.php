<div class="widget_content_filter">
	<div class="filter-container">
		<form action="<?php echo cmsCore::getInstance()->uri_absolute; ?>" method="post">
			<?php echo html_input('hidden', 'page', 1); ?>
			<div class="fields">
				<?php foreach($fields as $name => $field){ ?>
					<?php if (!$field['is_in_filter']){ continue; } ?>
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
				<?php if (!empty($props_fields)){ ?>
					<?php foreach($props as $prop){ ?>
						<?php
							if (!$prop['is_in_filter']){ continue; }
							$field = $props_fields[$prop['id']];
							$field->setName("p{$prop['id']}");
							if ($prop['type'] == 'list' && !empty($prop['options']['is_filter_multi'])){ $field->setOption('filter_multiple', true); }
							if ($prop['type'] == 'number' && !empty($prop['options']['is_filter_range'])){ $field->setOption('filter_range', true); }
							$value = isset($filters["p{$prop['id']}"]) ? $filters["p{$prop['id']}"] : null;
						?>
						<div class="field ft_<?php echo $prop['type']; ?> f_prop_<?php echo $prop['id']; ?>">
							<div class="title"><?php echo $prop['title']; ?></div>
							<div class="value">
								<?php echo $field->getFilterInput($value); ?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="buttons">
				<?php echo html_submit(LANG_FILTER_APPLY); ?>					
				<?php if (sizeof($filters)){ ?>
						<a href="<?php echo is_array($page_url) ? $page_url['base'] : $page_url; ?>"><?php echo LANG_CANCEL; ?></a>
				<?php } ?>				
			</div>
		</form>
	</div>
</div>