<?php $this->addTplJSNameFromContext('jquery-cookie'); ?>
<?php if ((!isset($attributes['toolbar']) || $attributes['toolbar']) && $this->isToolbar()){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<form id="<?php echo $attributes['form_id']; ?>" action="<?php echo $attributes['action']; ?>"
      method="<?php echo $attributes['method']; ?>"
      <?php if ($this->controller->request->isAjax()){ ?>
        class="ajax-form"
      <?php } ?>
      enctype="multipart/form-data"
      accept-charset="utf-8">

    <?php echo html_csrf_token(); ?>

    <?php echo $attributes['prepend_html']; ?>

    <div class="<?php if($form->is_tabbed){ ?>tabs-menu <?php } else { ?><?php if(count($form->getStructure()) > 1) { ?> without-tabs<?php } ?> card mb-0 rounded-0 <?php } ?>form-tabs">

        <?php if($form->is_tabbed){ ?>
            <ul class="nav nav-tabs flex-wrap" role="tablist">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (empty($fieldset['childs'])) { continue; } ?>
                    <li class="nav-item">
                        <a class="nav-link<?php if(empty($active_tab)){ $active_tab = true; ?> active<?php } ?>" href="#tab-<?php echo $fieldset_id; ?>" data-toggle="tab" role="tab">
                            <?php echo $fieldset['title']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
        <?php } else { ?>
             <div class="card-body">
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

    <div class="buttons my-3">
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
        <?php if ($attributes['cancel']['show']) { echo html_button($attributes['cancel']['title'], 'cancel', "location.href='{$attributes['cancel']['href']}'", array('class'=>'button-cancel')); } ?>
    </div>

</form>
<?php ob_start(); ?>
<script type="text/javascript">
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
        <?php if ($form->show_unsave_notice){ ?>
            icms.forms.initUnsaveNotice();
        <?php } ?>
        icms.forms.initCollapsedFieldset();
    <?php if (!empty($attributes['is_ajax'])){ ?>
        $('#<?php echo $attributes['form_id']; ?>').on('submit', function (){
            return icms.forms.submitAjax(this, <?php echo !empty($attributes['params']) ? json_encode($attributes['params']) : 'undefined'; ?>);
        });
    <?php } ?>
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>