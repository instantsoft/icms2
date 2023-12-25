<?php $this->addTplJSNameFromContext('jquery-cookie'); ?>
<?php $this->addTplJSNameFromContext('form-translate'); ?>
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

    <div class="<?php if($form->is_tabbed){ ?>tabs-menu mb-3 <?php } else { ?><?php if(count($form->getStructure()) > 1) { ?> without-tabs <?php } ?> card mb-0 <?php } ?>form-tabs">

        <?php if($form->is_tabbed){ ?>
            <ul class="nav nav-tabs flex-wrap">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (empty($fieldset['is_empty']) && empty($fieldset['childs'])) { continue; } ?>
                    <li class="nav-item">
                        <?php if($active_tab === false){ $active_tab = (string)$fieldset_id; } ?>
                        <a class="nav-link<?php if($active_tab === (string)$fieldset_id){ ?> active<?php } ?><?php if(!empty($fieldset['parent']['list'])){ ?> icms-form-tab__demand<?php } ?>" <?php if(!empty($fieldset['parent']['list'])){ ?>data-parent="<?php echo str_replace(':', '_', $fieldset['parent']['list']); ?>" data-parent_url="<?php echo $fieldset['parent']['url']; ?>"<?php } ?> href="#tab-<?php echo $fieldset_id; ?>" data-toggle="tab" data-fieldset_id="<?php echo $fieldset_id; ?>">
                            <?php echo $fieldset['title']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
        <?php } else { ?>
             <div class="card-body">
        <?php } ?>

        <?php include $this->getTemplateFileName('assets/ui/form_fields'); ?>

        </div>

    </div>

    <?php if(!empty($attributes['hook'])){ ?>

        <?php $param = empty($attributes['hook']['param']) ? false : $attributes['hook']['param'];  ?>
        <?php $hooks_html = cmsEventsManager::hookAll($attributes['hook']['event'], $param); ?>
        <?php if ($hooks_html) { echo html_each($hooks_html); } ?>

    <?php } ?>

    <?php echo $attributes['append_html']; ?>

    <div class="buttons <?php if (!$this->controller->request->isAjax()){ ?>my-3<?php } ?>">
        <?php if ($attributes['submit']['show']) { ?>
            <?php echo html_submit($attributes['submit']['title'], 'submit', $attributes['submit']); ?>
        <?php } ?>
        <?php if(isset($attributes['buttons'])){ ?>
            <?php foreach ($attributes['buttons'] as $button) { ?>
                <?php if (!empty($button['hide'])) { continue; } ?>
                <?php echo html_button(
                        $button['title'],
                        $button['name'],
                        (isset($button['onclick']) ? $button['onclick'] : ''),
                        (isset($button['attributes']) ? $button['attributes'] : array())
                    ); ?>
            <?php } ?>
        <?php } ?>
        <?php if ($attributes['cancel']['show']) { echo html_button($attributes['cancel']['title'], 'cancel', "location.href='{$attributes['cancel']['href']}'", array('class'=>'btn-secondary button-cancel')); } ?>
    </div>

</form>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE', 'LANG_TRANSLATE'); ?>
    icms.translate.url = '<?php echo href_to('languages', 'tr'); ?>';
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
    <?php if ($form->is_tabbed){ ?>
        $('#<?php echo $attributes['form_id']; ?> a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
            $.cookie('icms[<?php echo $cookie_tab_key; ?>]', $(this).data('fieldset_id'));
        });
    <?php } ?>
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
