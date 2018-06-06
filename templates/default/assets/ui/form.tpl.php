<?php $this->addJS($this->getJavascriptFileName('jquery-cookie')); ?>
<?php if ((!isset($attributes['toolbar']) || $attributes['toolbar']) && $this->isToolbar()){ ?>
    <div class="cp_toolbar">
        <?php $this->toolbar(); ?>
    </div>
<?php } ?>

<?php

    $is_ajax = $attributes['method']=='ajax';
    $method = $is_ajax ? 'post' : $attributes['method'];

    $default_submit = array('title' => LANG_SAVE, 'show' => true);
    $default_cancel = array('title' => LANG_CANCEL, 'href' => href_to_home(), 'show' => false);

    $submit = isset($attributes['submit']) ? array_merge($default_submit, $attributes['submit']) : $default_submit;
    $cancel = isset($attributes['cancel']) ? array_merge($default_cancel, $attributes['cancel']) : $default_cancel;

    $prepend_html = isset($attributes['prepend_html']) ? $attributes['prepend_html'] : '';
    $append_html = isset($attributes['append_html']) ? $attributes['append_html'] : '';

    $form_id = isset($attributes['form_id']) ? $attributes['form_id'] : md5(microtime(true));
    $index = 0;

    $visible_depend = array();
?>
<form id="<?php echo $form_id; ?>" action="<?php echo $attributes['action']; ?>"
      method="<?php echo $method; ?>"
      <?php if ($is_ajax){ ?>
        class="modal"
      <?php } ?>
      enctype="multipart/form-data"
      accept-charset="utf-8">

    <?php echo html_csrf_token(); ?>

    <?php echo $prepend_html; ?>

    <div class="<?php if($form->is_tabbed){ ?>tabs-menu <?php } ?>form-tabs">

        <?php if($form->is_tabbed){ ?>
            <ul class="tabbed">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (empty($fieldset['childs'])) { continue; } ?>
                    <li><a href="#tab-<?php echo $fieldset_id; ?>"><?php echo $fieldset['title']; ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>

        <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>

        <?php if ($fieldset['type'] == 'html'){ ?>
            <div id="fset_<?php echo $fieldset_id; ?>"><?php if (!empty($fieldset['content'])) { echo $fieldset['content']; } ?></div>
            <?php continue; ?>
        <?php } ?>

        <?php if (empty($fieldset['is_empty']) && empty($fieldset['childs'])) { continue; } ?>

            <div id="tab-<?php echo $fieldset_id; ?>" class="tab" <?php if($form->is_tabbed && $index){ ?>style="display: none;"<?php } ?>>
            <fieldset id="fset_<?php echo $fieldset_id; ?>" class="<?php if (!empty($fieldset['is_collapsed'])){ ?>is_collapsed <?php if (!empty($fieldset['collapse_open'])){ ?>do_expand<?php } else { ?>is_collapse<?php } ?><?php } ?><?php if (isset($fieldset['class'])){ ?><?php echo $fieldset['class']; ?><?php } ?>"
            <?php if (isset($fieldset['is_hidden'])){ ?>style="display:none"<?php } ?>>

                <?php if (!empty($fieldset['title']) && !$form->is_tabbed){ ?>
                    <legend><?php echo $fieldset['title']; ?></legend>
                <?php } ?>

                <?php if (is_array($fieldset['childs'])){ ?>
                <?php foreach($fieldset['childs'] as $field) { ?>

                    <?php

                        if ($data) { $field->setItem($data); }

                        $name = $field->getName();

                        if (is_array($errors) && isset($errors[$name])){
                            $error = $errors[$name];
                        } else {
                            $error = false;
                        }

                        $value = $field->getDefaultValue();
                        $rel = isset($field->rel) ? $field->rel : null;

                        if (strpos($name, ':') !== false){
                            $name_parts = explode(':', $name);
                            $_value = array_value_recursive($name_parts, $data);
                            if ($_value !== null){
                                $value = $_value;
                            }
                            $name = array_shift($name_parts) . '[' . implode('][', $name_parts) . ']';
                        } else {
                            if (is_array($data) && array_key_exists($name, $data)){
                                $value = $data[$name];
                            }
                        }

                        $classes = array(
                            'field',
                            'ft_'.strtolower(substr(get_class($field), 5))
                        );

                        if($field->getOption('is_required')){ $classes[] = 'reguired_field'; }

                        if ($error){
                            $classes[] = 'field_error';
                        }

                        if (!empty($field->groups_edit)){
                            if (!in_array(0, $field->groups_edit)){
                                $classes[] = 'groups-limit';
                                foreach($field->groups_edit as $group_id){
                                    $classes[] = 'group-' . $group_id;
                                }
                            }
                        }

                        $styles = array();

                        if (isset($field->is_visible)){
                            if (!$field->is_visible){
                                $styles[] = 'display:none';
                            }
                        }

                        if($field->visible_depend){
                            $visible_depend[] = $field;
                            $classes[] = 'child_field';
                        }

                        $classes = implode(' ', $classes);
                        $styles = implode(';', $styles);
                        $id = 'f_'.$field->id;

                    ?>

                    <div id="<?php echo $id; ?>" class="<?php echo $classes; ?>" <?php if ($rel) { ?>rel="<?php echo $rel; ?>"<?php } ?> <?php if ($styles) { ?>style="<?php echo $styles; ?>"<?php } ?>>

                        <?php if (!$field->is_hidden && !$field->getOption('is_hidden')) { ?>

                            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>

                            <?php echo $field->getInput($value); ?>

                            <?php if(!empty($field->hint)) { ?><div class="hint"><?php echo $field->hint; ?></div><?php } ?>

                        <?php } else { ?>

                            <?php echo html_input('hidden', $name, $value, array('id' => $name)); ?>

                        <?php } ?>

                    </div>

                <?php } ?>
                <?php } ?>

            </fieldset>
        </div>

        <?php $index++; } ?>

    </div>

        <script type="text/javascript">
            <?php echo $this->getLangJS('LANG_CH1','LANG_CH2','LANG_CH10', 'LANG_ISLEFT', 'LANG_SUBMIT_NOT_SAVE'); ?>
            $(function (){
                icms.forms.initUnsaveNotice();
            <?php if ($form->is_tabbed){ ?>
                initTabs('#<?php echo $form_id; ?>');
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
        <?php if($visible_depend){ foreach($visible_depend as $field){ ?>
                icms.forms.addVisibleDepend('<?php echo $form_id; ?>', '<?php echo $field->name; ?>', <?php echo json_encode($field->visible_depend); ?>);
            <?php } ?>
                icms.forms.VDReInit();
            <?php } ?>
            });
        </script>

    <?php if(!empty($attributes['hook'])){ ?>

        <?php $param = empty($attributes['hook']['param']) ? false : $attributes['hook']['param'];  ?>
        <?php $hooks_html = cmsEventsManager::hookAll($attributes['hook']['event'], $param); ?>
        <?php if ($hooks_html) { echo html_each($hooks_html); } ?>

    <?php } ?>

    <?php echo $append_html; ?>

    <div class="buttons">
        <?php if ($submit['show']) { ?>
            <?php echo html_submit($submit['title'], 'submit', $submit); ?>
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
        <?php if ($cancel['show']) { echo html_button($cancel['title'], 'cancel', "location.href='{$cancel['href']}'", array('class'=>'button-cancel')); } ?>
    </div>

</form>
<?php if ($is_ajax){ ?>
    <script type="text/javascript">
        $(function (){
            $('#<?php echo $form_id; ?>').on('submit', function (){
                return icms.forms.submitAjax(this, <?php echo !empty($attributes['params']) ? json_encode($attributes['params']) : 'undefined'; ?>);
            });
        });
    </script>
<?php }
