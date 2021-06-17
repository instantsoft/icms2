<div class="icms-widget__content_filter">
    <form action="<?php echo $page_url; ?>" id="<?php echo $form_id; ?>" method="get" accept-charset="utf-8">
        <?php echo html_input('hidden', 'page', 1); ?>
        <div class="fields form-row">
            <?php foreach($fields as $name => $field){ ?>
                <?php $value = isset($filters[$name]) ? $filters[$name] : null; ?>
                <?php $output = $field['handler']->getFilterInput($value); ?>
                <?php if (!$output){ continue; } ?>
                <div class="form-group col-md-12 field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                    <label class="font-weight-bold clickable" data-toggle="collapse" data-target="#collapse_<?php echo $field['name']; ?>">
                        <span class="collapse__angle-down"><?php html_svg_icon('solid', 'angle-down'); ?></span>
                        <span class="collapse__angle-up"><?php html_svg_icon('solid', 'angle-up'); ?></span>
                        <?php echo $field['title']; ?>
                    </label>
                    <div id="collapse_<?php echo $field['name']; ?>" class="collapse show"><?php echo $output; ?></div>
                </div>
            <?php } ?>
            <?php if (!empty($props)){ ?>
                <?php foreach($props as $prop){ ?>
                    <?php $value = isset($filters["p{$prop['id']}"]) ? $filters["p{$prop['id']}"] : null; ?>
                    <?php $output = $prop['handler']->getFilterInput($value); ?>
                    <?php if (!$output){ continue; } ?>
                    <div class="form-group col-md-12 field ft_<?php echo $prop['type']; ?> f_prop_<?php echo $prop['id']; ?>">
                        <label class="font-weight-bold clickable" data-toggle="collapse" data-target="#collapse_p<?php echo $prop['id']; ?>">
                            <span class="collapse__angle-down"><?php html_svg_icon('solid', 'angle-down'); ?></span>
                            <span class="collapse__angle-up"><?php html_svg_icon('solid', 'angle-up'); ?></span>
                            <?php echo $prop['title']; ?>
                        </label>
                        <div id="collapse_p<?php echo $prop['id']; ?>" class="collapse show"><?php echo $output; ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="buttons d-flex">
            <?php echo html_submit(LANG_FILTER_APPLY); ?>
            <?php if (count($filters)){ ?>
                <a class="btn btn-secondary cancel_filter_link mx-2" href="<?php echo $page_url; ?>"><?php echo LANG_CANCEL; ?></a>
            <?php } ?>
        </div>
    </form>
</div>
<?php ob_start(); ?>
<script>
    $(function (){
        icms.forms.initFilterForm('#<?php echo $form_id; ?>');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>