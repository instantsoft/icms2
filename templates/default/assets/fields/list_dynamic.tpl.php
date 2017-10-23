<?php $this->addJSFromContext('templates/default/js/fields/list_dynamic.js'); ?>
<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div id="list_wrap_<?php echo $field->id; ?>" class="dynamic_list_wrap">

    <div class="list_wrap"></div>

    <div class="add_list" style="display:none">
        <?php echo $field->select_title; ?>:
        <select></select>
        <a class="ajaxlink add_value" href="#"><?php echo LANG_APPLY; ?></a> |
        <a class="ajaxlink cancel" href="#"><?php echo LANG_CANCEL; ?></a>
    </div>

    <a class="ajaxlink add_link" href="#"><?php echo LANG_ADD; ?></a>

    <div class="list_template" style="display:none">
        <span class="title"></span>
        <span class="to"><select name=""></select></span>
        <span class="delete"><a class="ajaxlink unset_value" href="#"><?php echo LANG_CANCEL; ?></a></span>
    </div>
    <select class="value_items_list" style="display:none">
        <?php foreach($field->data['value_items'] as $k => $v){ ?>
            <option value="<?php echo $k; ?>"><?php html($v); ?></option>
        <?php } ?>
    </select>
    <select class="key_items_list" style="display:none">
        <?php foreach($field->data['items'] as $k => $v){ ?>
            <option id="key_option_<?php echo $k; ?>" value="<?php echo $k; ?>"><?php html($v); ?></option>
        <?php } ?>
    </select>

</div>

<script type="text/javascript">
    $(function(){
        new icms.dynamicList('<?php echo $field->id; ?>', '<?php echo $field->element_name; ?>', <?php echo json_encode($value); ?>, <?php echo (int)$field->single_select; ?>);
    });
</script>