<?php if ((!isset($attributes['toolbar']) || $attributes['toolbar']) && $this->isToolbar()){ ?>
    <div class="cp_toolbar">
        <?php $this->toolbar(); ?>
    </div>
<?php } ?>

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
        class="modal"
      <?php } ?>
      enctype="multipart/form-data"
      accept-charset="utf-8">

    <?php echo html_csrf_token(); ?>

    <?php echo $prepend_html; ?>

    <div id="form-tabs" <?php if($form->is_tabbed){ ?>class="tabs-menu"<?php } ?>>

        <?php if($form->is_tabbed){ ?>
            <ul class="tabbed">
                <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>
                    <?php if (!isset($fieldset['childs']) || !sizeof($fieldset['childs'])) { continue; } ?>
                    <li><a href="#tab-<?php echo $fieldset_id; ?>"><?php echo $fieldset['title']; ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>

        <?php foreach($form->getStructure() as $fieldset_id => $fieldset){ ?>

        <?php if ($fieldset['type'] == 'html'){ ?>
            <div id="fset_<?php echo $fieldset_id; ?>"><?php if (!empty($fieldset['content'])) { echo $fieldset['content']; } ?></div>
            <?php continue; ?>
        <?php } ?>

        <?php if (empty($fieldset['is_empty']) && (!isset($fieldset['childs']) || !sizeof($fieldset['childs']))) { continue; } ?>

        <div id="tab-<?php echo $fieldset_id; ?>" class="tab">
            <fieldset id="fset_<?php echo $fieldset_id; ?>"
            <?php if (isset($fieldset['class'])){ ?>class="<?php echo $fieldset['class']; ?>"<?php } ?>
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

                        $default = $field->getDefaultValue();
                        $rel = isset($field->rel) ? $field->rel : null;

                        if (strstr($name, ':')){
                            $name_parts = explode(':', $name);
                            $name       = $name_parts[0].'['.$name_parts[1].']';
                            if (isset($data[$name_parts[0]]) && @array_key_exists($name_parts[1], $data[$name_parts[0]])){
                                $value = $data[$name_parts[0]][$name_parts[1]];
                            } else {
                                $value = $default;
                            }
                        } else {
                            if (is_array($data) && @array_key_exists($name, $data)){
                                $value = $data[$name];
                            } else {
                                $value = $default;
                            }
                        }

                        $classes = array(
                            'field',
                            'ft_' . mb_strtolower(mb_substr(get_class($field), 5))
                        );

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

                        $classes = implode(' ', $classes);
                        $styles = implode(';', $styles);
                        $id = "f_{$field->id}";

                    ?>

                    <div id="<?php echo $id; ?>" class="<?php echo $classes; ?>" <?php if ($rel) { ?>rel="<?php echo $rel; ?>"<?php } ?> <?php if ($styles) { ?>style="<?php echo $styles; ?>"<?php } ?>>

                        <?php if (!$field->is_hidden && !$field->getOption('is_hidden')) { ?>

                            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>

                            <?php echo $field->getInput($value); ?>

                            <?php if(!empty($field->hint)) { ?><div class="hint"><?php echo $field->hint; ?></div><?php } ?>

                        <?php } else { ?>

                            <?php echo html_input('hidden', $name, $value); ?>

                        <?php } ?>

                    </div>

                <?php } ?>
                <?php } ?>

            </fieldset>
        </div>

        <?php } ?>

    </div>

    <?php if ($form->is_tabbed){ ?>
        <script>

                $('#form-tabs .tab').hide();
                $('#form-tabs .tab').eq(0).show();
                $('#form-tabs > ul > li').eq(0).addClass('active');

                $('#form-tabs > ul > li > a').click(function(){
                    $('#form-tabs li').removeClass('active');
                    $(this).parent('li').addClass('active');
                    $('#form-tabs .tab').hide();
                    $('#form-tabs '+$(this).attr('href')).show();
                    return false;
                });

        </script>
    <?php } ?>

    <?php if(!empty($attributes['hook'])){ ?>

        <?php $param = empty($attributes['hook']['param']) ? false : $attributes['hook']['param'];  ?>
        <?php $hooks_html = cmsEventsManager::hookAll($attributes['hook']['event'], $param); ?>
        <?php if ($hooks_html) { echo html_each($hooks_html); } ?>

    <?php } ?>

    <?php echo $append_html; ?>

    <div class="buttons">
        <?php echo html_submit($submit['title'], 'submit', $submit); ?>
        <?php if ($cancel['show']) { echo html_button($cancel['title'], 'cancel', "location.href='{$cancel['href']}'"); } ?>
    </div>

</form>
<?php if ($is_ajax){ ?>
    <script type="text/javascript">
        $(function (){
            $('#<?php echo $form_id; ?>').on('submit', function (){
                return icms.forms.submitAjax(this);
            });
        });
    </script>
<?php } ?>