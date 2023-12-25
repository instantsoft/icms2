<?php $this->addTplJSNameFromContext('jquery-cookie'); ?>
<?php if ((!isset($attributes['toolbar']) || $attributes['toolbar']) && $this->isToolbar() && empty($attributes['hide_toolbar'])){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<form id="<?php html($attributes['form_id']); ?>" action="<?php html($attributes['action']); ?>"
      method="<?php echo $attributes['method']; ?>"
      class="<?php html($attributes['form_class']); ?><?php if ($this->controller->request->isAjax()){ ?> ajax-form<?php } ?>"
      enctype="multipart/form-data"
      accept-charset="utf-8">

    <?php echo html_csrf_token(); ?>

    <?php echo $attributes['prepend_html']; ?>

    <div class="<?php if($form->is_tabbed){ ?>tabs-menu mb-3 <?php } else { ?><?php if(count($form->getStructure()) > 1) { ?> without-tabs <?php } ?> <?php } ?>form-tabs">

        <?php if($form->is_tabbed){ ?>
            <ul class="nav nav-tabs flex-wrap" role="tablist">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (empty($fieldset['is_empty']) && empty($fieldset['childs'])) { continue; } ?>
                    <li class="nav-item">
                        <?php if($active_tab === false){ $active_tab = $fieldset_id; } ?>
                        <a class="nav-link<?php if($active_tab === $fieldset_id){ ?> active<?php } ?><?php if(!empty($fieldset['parent']['list'])){ ?> icms-form-tab__demand<?php } ?>" <?php if(!empty($fieldset['parent']['list'])){ ?>data-parent="<?php echo str_replace(':', '_', $fieldset['parent']['list']); ?>" data-parent_url="<?php echo $fieldset['parent']['url']; ?>"<?php } ?> href="#tab-<?php echo $fieldset_id; ?>" data-toggle="tab" role="tab">
                            <?php echo $fieldset['title']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
        <?php } else { ?>
             <div class="icms-form-body">
        <?php } ?>

        <?php include 'form_fields.tpl.php'; ?>

        </div>

    </div>

    <?php if(!empty($attributes['hook'])){ ?>

        <?php $param = empty($attributes['hook']['param']) ? false : $attributes['hook']['param'];  ?>
        <?php $hooks_html = cmsEventsManager::hookAll($attributes['hook']['event'], $param); ?>
        <?php if ($hooks_html) { echo html_each($hooks_html); } ?>

    <?php } ?>

    <?php echo $attributes['append_html']; ?>

    <div class="buttons <?php if (!$this->controller->request->isAjax()){ ?>mt-3 mt-md-4<?php } ?>">
        <?php if ($attributes['submit']['show']) { unset($attributes['submit']['show']); ?>
            <?php echo html_submit($attributes['submit']['title'], 'submit', $attributes['submit']); ?>
        <?php } ?>
        <?php if ($attributes['cancel']['show']) { echo html_button($attributes['cancel']['title'], 'cancel', "location.href='{$attributes['cancel']['href']}'", ['class'=>'btn-secondary button-cancel']); } ?>
        <?php if(!empty($attributes['buttons'])){ ?>

            <?php $many_buttons = count($attributes['buttons']) > 1; ?>

            <?php if($many_buttons){ ?>
                <div class="dropdown d-inline-block dropup">
                    <button class="btn btn-secondary" type="button" data-toggle="dropdown" data-display="static">
                        <?php html_svg_icon('solid', 'ellipsis-h'); ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
            <?php } ?>

            <?php foreach ($attributes['buttons'] as $button) {

                if (!empty($button['hide'])) { continue; }

                if ($many_buttons){
                    $button['attributes']['class'] = isset($button['attributes']['class']) ? $button['attributes']['class'] .= ' dropdown-item' : 'dropdown-item';
                }

                echo html_button(
                    $button['title'],
                    $button['name'],
                    (isset($button['onclick']) ? $button['onclick'] : ''),
                    (isset($button['attributes']) ? $button['attributes'] : [])
                );
            } ?>

            <?php if($many_buttons){ ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>

</form>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
        <?php if ($form->show_unsave_notice){ ?>
            icms.forms.initUnsaveNotice();
        <?php } ?>
        icms.forms.initCollapsedFieldset('<?php echo $attributes['form_id']; ?>');
        icms.forms.initFormHelpers();
    <?php if (!empty($attributes['is_ajax'])){ ?>
        $('#<?php echo $attributes['form_id']; ?>').on('submit', function (){
            return icms.forms.submitAjax(this, <?php echo !empty($attributes['params']) ? json_encode($attributes['params']) : 'undefined'; ?>);
        });
    <?php } ?>
        icms.forms.initFieldsetChildList('<?php echo $attributes['form_id']; ?>');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
