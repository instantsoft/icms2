<div class="datagrid_dataset_filter">
    <form onsubmit="return icms.datagrid.applyAdvancedFilter(this)">

    <?php $index = 0; ?>
    <?php foreach($fields as $field) { ?>
        <?php if (!$field['handler']->filter_type) { continue; } ?>
        <?php if ($field['handler']->is_virtual) { continue; } ?>
        <?php if ($field['name'] === 'user') { $field['name'] = 'user_id'; } ?>
        <?php echo html_input('hidden', "filters[{$index}][field]", $field['name']); ?>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label"><?php html($field['title']); ?></label>
            <div class="col-sm-3">
                <?php if ($field['handler']->filter_type === 'int') { ?>
                    <select class="custom-select form-control form-control-sm" name="filters[<?php echo $index; ?>][condition]">
                        <option value="eq">=</option>
                        <option value="gt">&gt;</option>
                        <option value="lt">&lt;</option>
                        <option value="ge">&ge;</option>
                        <option value="le">&le;</option>
                        <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
                        <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
                    </select>
                <?php } ?>
                <?php if ($field['handler']->filter_type === 'str') { ?>
                    <select class="custom-select form-control form-control-sm" name="filters[<?php echo $index; ?>][condition]">
                        <option value="lk"><?php echo LANG_FILTER_LIKE; ?></option>
                        <option value="eq">=</option>
                        <option value="ln"><?php echo LANG_FILTER_NOT_LIKE; ?></option>
                        <option value="lb"><?php echo LANG_FILTER_LIKE_BEGIN; ?></option>
                        <option value="lf"><?php echo LANG_FILTER_LIKE_END; ?></option>
                        <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
                        <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
                    </select>
                <?php } ?>
                <?php if ($field['handler']->filter_type === 'date') { ?>
                    <select class="custom-select form-control form-control-sm" name="filters[<?php echo $index; ?>][condition]">
                        <option value="eq">=</option>
                        <option value="gt">&gt;</option>
                        <option value="lt">&lt;</option>
                        <option value="ge">&ge;</option>
                        <option value="le">&le;</option>
                        <option value="dy"><?php echo LANG_FILTER_DATE_YOUNGER; ?></option>
                        <option value="do"><?php echo LANG_FILTER_DATE_OLDER; ?></option>
                        <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
                        <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
                    </select>
                <?php } ?>
            </div>
            <div class="col-sm-6">
                <?php
                    echo html_input('text', "filters[{$index}][value]", '', [
                        'autocomplete' => 'off',
                        'placeholder' => $field['handler']->filter_hint
                    ]);
                ?>
            </div>
        </div>
        <?php $index++; ?>
    <?php } ?>

    <?php echo html_submit(LANG_APPLY); ?>

    </form>
</div>