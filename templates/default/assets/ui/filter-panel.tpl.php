<?php $user = cmsUser::getInstance(); ?>
<?php if (!isset($is_expanded)){ $is_expanded = false; } unset($filters['user_id']); ?>
<?php $form_url = is_array($page_url) ? $page_url['base'] : $page_url; $form_url_sep = strpos($form_url, '?') === false ? '?' : '&'; ?>
<div class="filter-panel gui-panel <?php echo $css_prefix;?>-filter">
    <div class="filter-link" <?php if($filters || $is_expanded){ ?>style="display:none"<?php } ?>>
        <a href="javascript:toggleFilter()"><span><?php echo LANG_SHOW_FILTER; ?></span></a>
    </div>
    <div class="filter-container" <?php if(!$filters && !$is_expanded){ ?>style="display:none"<?php } ?>>
		<div class="filter-close">
            <a href="javascript:toggleFilter();"><span><?php echo LANG_CLOSE; ?></span></a>
        </div>
        <form action="<?php echo $form_url; ?>" method="get">
            <?php echo html_input('hidden', 'page', 1); ?>
            <?php if(!empty($ext_hidden_params)){ ?>
                <?php foreach($ext_hidden_params as $fname => $fvalue){ ?>
                    <?php echo html_input('hidden', $fname, $fvalue); ?>
                    <?php if($filters){ $filters[$fname] = $fvalue; } ?>
                <?php } ?>
            <?php } ?>
            <div class="fields">
                <?php $fields_count = 0; ?>
                <?php foreach($fields as $name => $field){ ?>
                    <?php if (!$field['is_in_filter']){ continue; } ?>
                    <?php if (!empty($field['filter_view']) && !$user->isInGroups($field['filter_view'])) { continue; } ?>
                    <?php $value = isset($filters[$name]) ? $filters[$name] : null; ?>
                    <?php $output = $field['handler']->setItem(array('ctype_name' => $css_prefix, 'id' => null))->getFilterInput($value); ?>
                    <?php if (!$output){ continue; } ?>
                    <?php $fields_count++; ?>
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
                            $fields_count++;
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
            <?php if ($fields_count) { ?>
                <div class="buttons">
                    <?php echo html_submit(LANG_FILTER_APPLY); ?>
                    <?php if (sizeof($filters)){ ?>
                        <div class="link">
                            <a href="<?php echo ((is_array($page_url) && !empty($page_url['cancel'])) ? $page_url['cancel'] : $form_url); ?>">
                                <?php echo LANG_CANCEL; ?>
                            </a>
                        </div>
                        <div class="link">
                            # <a href="<?php echo $form_url.$form_url_sep.http_build_query($filters); ?>"><?php echo LANG_FILTER_URL; ?></a>
                        </div>
                        <?php
                            $hooks_html = cmsEventsManager::hookAll('content_filter_buttons_html', array($css_prefix, $form_url, $filters));
                            if ($hooks_html) { echo html_each($hooks_html); }
                        ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php if (!$fields_count) { ?>
<script type="text/javascript">
    $(function (){
        $('.filter-panel.groups-filter').hide();
    });
</script>
<?php } ?>