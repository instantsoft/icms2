<?php
    $this->addTplJSNameFromContext('fields/fieldsgroup');
 ?>
<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div id="fieldsgroup_<?php echo $field->id; ?>">

    <div class="fieldsgroup_wrap"></div>

    <div class="d-flex align-items-center">
        <a class="btn btn-success add_link" href="#">
            <span><?php echo html_svg_icon('solid', 'plus-circle'); ?> <?php echo $field->add_title ?? LANG_ADD; ?></span>
        </a>
    </div>
    <template class="fieldsgroup-template">
        <div class="fieldsgroup-item">
            <div class="d-flex mb-3 bg-light p-2 p-md-3">
                <div class="form-row flex-grow-1">
                    <?php foreach($field->data['form']->getFormStructure($data) as $fieldset_id => $fieldset) { ?>
                        <?php foreach($fieldset['childs'] as $child_field) { ?>
                            <div class="form-field__child col-sm <?php echo implode(' ', $child_field->classes); ?>">
                                <?php echo $child_field->{$child_field->display_input}($child_field->getDefaultValue()); ?>
                                <?php if(!empty($child_field->hint)) { ?>
                                    <div class="hint form-text text-muted small mt-1">
                                        <?php echo $child_field->hint; ?>
                                    </div>
                                <?php } ?>
                                <div class="invalid-feedback w-auto ml-auto"></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="d-flex align-items-start">
                    <a href="#" class="ml-3 btn btn-sm btn-danger delete">
                        <?php echo html_svg_icon('solid', 'times'); ?>
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>

<?php ob_start(); ?>
<script>
    $(function(){
        new icms.fieldsgroup('<?php echo $field->id; ?>', '<?php echo $field->element_name; ?>', <?php echo json_encode($value); ?>, <?php echo json_encode($field->getError() ?? []); ?>);
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>