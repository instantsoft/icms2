<?php
$user = cmsUser::getInstance();
$form_id = isset($form_id) ? $form_id : md5(microtime(true));
if (!isset($is_expanded)){ $is_expanded = false; } unset($filters['user_id']);
$form_url = is_array($page_url) ? $page_url['base'] : $page_url;
$form_url_sep = strpos($form_url, '?') === false ? '?' : '&';
?>

<div class="icms-filter-panel gui-panel my-3 <?php echo $css_prefix;?>-filter">

    <button class="icms-filter-link__open btn btn-block btn-light text-left <?php if($filters || $is_expanded){ ?>d-none<?php } ?>">
        <?php html_svg_icon('solid', 'filter'); ?> <span><?php echo LANG_SHOW_FILTER; ?></span>
    </button>

    <div class="icms-filter-container p-3 bg-light position-relative <?php if(!$filters && !$is_expanded){ ?>d-none<?php } ?>">
        <button type="button" class="close position-absolute icms-filter-link__close" title="<?php echo LANG_CLOSE; ?>">
            <span>&times;</span>
        </button>
        <form action="<?php html($form_url); ?>" method="get" id="<?php html($form_id); ?>" accept-charset="utf-8">
            <?php echo html_input('hidden', 'page', 1); ?>
            <?php if(!empty($ext_hidden_params)){ ?>
                <?php foreach($ext_hidden_params as $fname => $fvalue){ ?>
                    <?php echo html_input('hidden', $fname, $fvalue); ?>
                    <?php if($filters){ $filters[$fname] = $fvalue; } ?>
                <?php } ?>
            <?php } ?>
            <div class="fields form-row">
                <?php $fields_count = 0; ?>
                <?php foreach($fields as $name => $field){ ?>
                    <?php if (!$field['is_in_filter']){ continue; } ?>
                    <?php if (!empty($field['filter_view']) && !$user->isInGroups($field['filter_view'])) { continue; } ?>
                    <?php $value = isset($filters[$name]) ? $filters[$name] : null; ?>
                    <?php $output = $field['handler']->getFilterInput($value); ?>
                    <?php if (!$output){ continue; } ?>
                    <?php $fields_count++; ?>
                    <div class="form-group col-md-6 field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                        <label class="font-weight-bold"><?php echo $field['title']; ?></label>
                        <?php echo $output; ?>
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
                        <div class="form-group col-md-6 field ft_<?php echo $prop['type']; ?> f_prop_<?php echo $prop['id']; ?>">
                            <label class="font-weight-bold"><?php echo $prop['title']; ?></label>
                            <?php echo $prop['handler']->getFilterInput($value); ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <?php if ($fields_count) { ?>
                <div class="buttons d-flex flex-column flex-md-row">
                    <?php echo html_submit(LANG_FILTER_APPLY); ?>
                    <?php if (count($filters)){ ?>
                        <a class="btn btn-secondary cancel_filter_link my-2 my-md-0 mx-md-2" href="<?php echo ((is_array($page_url) && !empty($page_url['cancel'])) ? $page_url['cancel'] : $form_url); ?>">
                            <?php echo LANG_CANCEL; ?>
                        </a>
                        <?php
                        if(!empty($page_url['filter_link'])){
                            $filter_url = $page_url['filter_link'];
                        } else {
                            $filter_url = $form_url.$form_url_sep.http_build_query($filters);
                        }
                        ?>
                        <a class="btn btn-link" href="<?php echo $filter_url; ?>">
                            # <?php echo LANG_FILTER_URL; ?>
                        </a>
                        <?php
                            $hooks_html = cmsEventsManager::hookAll('content_filter_buttons_html', array($css_prefix, $form_url, $filters));
                            if ($hooks_html) { ?>
                                <div class="mt-2 mt-md-0 ml-md-auto">
                                    <?php echo html_each($hooks_html); ?>
                                </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
        <?php if (!$fields_count) { ?>
            $('.icms-filter-panel.groups-filter').hide();
        <?php } ?>
        icms.forms.initFilterForm('#<?php echo $form_id; ?>');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>