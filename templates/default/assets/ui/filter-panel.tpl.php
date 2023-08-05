<?php $user = cmsUser::getInstance(); ?>
<?php $form_id = isset($form_id) ? $form_id : md5(microtime(true)); ?>
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
        <form action="<?php html($form_url); ?>" method="get" id="<?php echo $form_id; ?>" accept-charset="utf-8">
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
                    <?php $output = $field['handler']->getFilterInput($value); ?>
                    <?php if (!$output){ continue; } ?>
                    <?php $fields_count++; ?>
                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                        <div class="title"><?php echo $field['title']; ?></div>
                        <div class="value">
                            <?php echo $output; ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($props)){ ?>
                    <?php foreach($props as $prop){ ?>
                        <?php
                            if (!$prop['is_in_filter']){ continue; }
                            $fields_count++;
                            $prop['handler']->setName("p{$prop['id']}");
                            $value = isset($filters["p{$prop['id']}"]) ? $filters["p{$prop['id']}"] : null;
                        ?>
                        <div class="field ft_<?php echo $prop['type']; ?> f_prop_<?php echo $prop['id']; ?>">
                            <div class="title"><?php echo $prop['title']; ?></div>
                            <div class="value">
                                <?php echo $prop['handler']->getFilterInput($value); ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if ($fields_count) { ?>
                <div class="spinner filter_loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
                <div class="buttons">
                    <?php echo html_submit(LANG_FILTER_APPLY); ?>
                    <?php if (count($filters)){ ?>
                        <div class="link">
                            <a class="cancel_filter_link" href="<?php echo ((is_array($page_url) && !empty($page_url['cancel'])) ? $page_url['cancel'] : $form_url); ?>">
                                <?php echo LANG_CANCEL; ?>
                            </a>
                        </div>
                        <div class="link">
                            <?php
                            if(!empty($page_url['filter_link'])){
                                $filter_url = $page_url['filter_link'];
                            } else {
                                $filter_url = $form_url.$form_url_sep.http_build_query($filters);
                            }
                            ?>
                            # <a href="<?php echo $filter_url; ?>">
                                <?php echo LANG_FILTER_URL; ?>
                            </a>
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
<script>
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
        <?php if (!$fields_count) { ?>
            $('.filter-panel.groups-filter').hide();
        <?php } ?>
        icms.forms.initFilterForm('#<?php echo $form_id; ?>');
    });
</script>