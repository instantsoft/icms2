<<<<<<< HEAD
=======
<?php $this->addTplJSNameFromContext('jquery-cookie'); ?>
>>>>>>> origin/master
<?php if ((!isset($attributes['toolbar']) || $attributes['toolbar']) && $this->isToolbar()){ ?>
    <div class="cp_toolbar">
        <?php $this->toolbar(); ?>
    </div>
<?php } ?>

<<<<<<< HEAD
<?php

    $is_ajax = $attributes['method']=='ajax';
    $method = $is_ajax ? 'post' : $attributes['method'];

    $default_submit = array('title' => LANG_SAVE);
    $default_cancel = array('title' => LANG_CANCEL, 'href'=>href_to_home(), 'show'=>false);

    $submit = isset($attributes['submit']) ? array_merge($default_submit, $attributes['submit']) : $default_submit;
    $cancel = isset($attributes['cancel']) ? array_merge($default_cancel, $attributes['cancel']) : $default_cancel;

    $prepend_html = isset($attributes['prepend_html']) ? $attributes['prepend_html'] : '';
    $append_html = isset($attributes['append_html']) ? $attributes['append_html'] : '';

    $form_id = uniqid();

?>
<form id="<?php echo $form_id; ?>" action="<?php echo $attributes['action']; ?>"
      method="<?php echo $method; ?>"
      <?php if ($is_ajax){ ?>
=======
<form id="<?php echo $attributes['form_id']; ?>" action="<?php echo $attributes['action']; ?>"
      method="<?php echo $attributes['method']; ?>"
      <?php if ($this->controller->request->isAjax()){ ?>
>>>>>>> origin/master
        class="modal"
      <?php } ?>
      enctype="multipart/form-data"
      accept-charset="utf-8">

    <?php echo html_csrf_token(); ?>

    <?php echo $attributes['prepend_html']; ?>

    <div class="<?php if($form->is_tabbed){ ?>tabs-menu <?php } ?>form-tabs">

        <?php if($form->is_tabbed){ ?>
            <ul class="tabbed">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (empty($fieldset['childs'])) { continue; } ?>
                    <li><a href="#tab-<?php echo $fieldset_id; ?>"><?php echo $fieldset['title']; ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>

        <?php include 'form_fields.tpl.php'; ?>

    </div>

    <?php if(!empty($attributes['hook'])){ ?>

        <?php $param = empty($attributes['hook']['param']) ? false : $attributes['hook']['param'];  ?>
        <?php $hooks_html = cmsEventsManager::hookAll($attributes['hook']['event'], $param); ?>
        <?php if ($hooks_html) { echo html_each($hooks_html); } ?>

    <?php } ?>

    <?php echo $attributes['append_html']; ?>

    <div class="buttons">
<<<<<<< HEAD
        <?php echo html_submit($submit['title'], 'submit', $submit); ?>
        <?php if ($cancel['show']) { echo html_button($cancel['title'], 'cancel', "location.href='{$cancel['href']}'"); } ?>
=======
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
>>>>>>> origin/master
    </div>

</form>
<<<<<<< HEAD
<?php if ($is_ajax){ ?>
    <script type="text/javascript">
        $(function (){
            $('#<?php echo $form_id; ?>').on('submit', function (){
                return icms.forms.submitAjax(this);
            });
        });
    </script>
<?php } ?>
=======
<?php ob_start(); ?>
<script type="text/javascript">
    <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
    <?php if ($form->show_unsave_notice){ ?>
        icms.forms.initUnsaveNotice();
    <?php } ?>
    <?php if ($form->is_tabbed){ ?>
        initTabs('#<?php echo $attributes['form_id']; ?>');
    <?php } ?>
        $('.is_collapsed legend').on('click', function (){
            var _fieldset = $(this).closest('.is_collapsed');
            $(_fieldset).toggleClass('is_collapse do_expand');
            $.cookie('icms[fieldset_state]['+$(_fieldset).attr('id')+']', $(_fieldset).hasClass('do_expand'));
        });
        $('.is_collapsed').each(function (){
            if($(this).find('.field_error').length > 0 || $.cookie('icms[fieldset_state]['+$(this).attr('id')+']') === 'true'){
                $(this).addClass('do_expand').removeClass('is_collapse'); return;
            }
        });
    <?php if (!empty($attributes['is_ajax'])){ ?>
        $('#<?php echo $attributes['form_id']; ?>').on('submit', function (){
            return icms.forms.submitAjax(this, <?php echo !empty($attributes['params']) ? json_encode($attributes['params']) : 'undefined'; ?>);
        });
    <?php } ?>
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
>>>>>>> origin/master
