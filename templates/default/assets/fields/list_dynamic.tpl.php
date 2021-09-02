<?php
    $this->addTplJSNameFromContext('fields/list_dynamic');
 ?>
    <?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div id="list_wrap_<?php echo $field->id; ?>" class="dynamic_list_wrap">

    <div class="list_wrap"></div>

    <div class="add_list" style="display:none">
        <?php echo $field->select_title; ?>:
        <select></select>
        <a class="ajaxlink add_value" href="#"><?php echo LANG_APPLY; ?></a> |
        <a class="ajaxlink cancel" href="#"><?php echo LANG_CANCEL; ?></a>
    </div>

    <a class="ajaxlink add_link" href="#"><?php echo isset($field->add_title) ? $field->add_title : LANG_ADD; ?></a>

    <div class="list_template" style="display:none">
        <span class="title"><input type="hidden" name="" value=""></span>
        <span class="to"><select name=""></select></span>
        <span class="value"><input style="display:none" class="input" type="text" name="" value=""></span>
        <span class="delete"><a class="ajaxlink unset_value" href="#"><?php echo LANG_CANCEL; ?></a></span>
    </div>
    <?php if (!$field->data['is_ns_value_items']) { ?>
        <select class="value_items_list" style="display:none">
            <?php foreach($field->data['value_items'] as $k => $v){ ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
            <?php } ?>
        </select>
    <?php } else { ?>
        <?php foreach($field->data['value_items'] as $wrap_key => $value_items){ ?>
            <select class="value_items_list <?php echo $wrap_key; ?>" style="display:none">
                <?php foreach($value_items as $k => $v){ ?>
                    <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php } ?>
            </select>
        <?php } ?>
    <?php } ?>
    <select class="key_items_list" style="display:none">
        <?php foreach($field->data['items'] as $k => $v){ ?>
            <?php
                $data_attr = ''; $title = $v;
                if(is_array($v)){
                    $title = $v['title'];
                    if (!empty($v['data'])) {
                        foreach ($v['data'] as $key => $val) {
                            $data_attr .= 'data-'.$key.'="'.htmlspecialchars($val).'" ';
                        }
                    }
                }
            ?>
            <option id="key_option_<?php echo $field->id; ?>_<?php echo $k; ?>" value="<?php echo $k; ?>" <?php echo $data_attr; ?>>
                <?php echo $title; ?>
            </option>
        <?php } ?>
    </select>

</div>
<?php ob_start(); ?>
<script>
    $(function(){
        new icms.dynamicList('<?php echo $field->id; ?>', '<?php echo $field->element_name; ?>', <?php echo json_encode($value); ?>, <?php echo json_encode($field->multiple_keys); ?>, <?php echo !isset($field->single_select) ? 1 : (int)$field->single_select; ?>);
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>