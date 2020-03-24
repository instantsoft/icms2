<div class="modal_padding datagrid_grid_columns_settings">

    <form onsubmit="return contentGridColumnsSettings()" action="<?php echo $this->href_to('content', array('grid_columns', $ctype_id)); ?>">
<?php foreach($items as $type => $fields){ ?>

        <h4><?php echo constant('LANG_CP_GRID_COLYMNS_'.strtoupper($type)); ?></h4>
        <div class="form-group row mb-2 d-none d-sm-flex">
            <label class="col-sm-4 font-weight-bold">
                <?php echo LANG_TITLE; ?>
            </label>
            <div class="col-sm-4 font-weight-bold">
                <?php echo LANG_CP_GRID_COLYMNS_FILTER; ?>
            </div>
            <div class="col-sm-4 font-weight-bold">
                <?php echo LANG_CP_GRID_COLYMNS_DISPLAY; ?>
            </div>
        </div>
        <?php foreach($fields as $name => $field){ ?>
            <div class="form-group row">
                <div class="col-sm-4">
                    <div class="custom-control custom-switch">
                        <?php echo html_checkbox('columns['.$type.']['.$name.'][enabled]', !empty($config[$type][$name]['enabled']), 1, ['id' => 'ch'.$type.$name, 'class' => 'custom-control-input']); ?>
                        <label class="custom-control-label" for="<?php echo 'ch'.$type.$name; ?>"><?php html($field['title']); ?></label>
                        <?php echo html_input('hidden', "columns[{$type}][{$name}][field]", $name); ?>
                    </div>
                </div>
                <div class="col-sm-4 my-2 my-sm-0">
                    <?php
                    if(!empty($field['filters'])){
                        $field['filters'] = array(''=>LANG_SELECT) + $field['filters'];
                        $selected = !empty($config[$type][$name]['filter']) ? $config[$type][$name]['filter'] : '';
                        echo html_select("columns[{$type}][{$name}][filter]", $field['filters'], $selected, ['class' => 'custom-select form-control-sm']);
                    } else {
                        echo ' &mdash;';
                    } ?>
                </div>
                <div class="col-sm-4">
                    <?php
                    if(!empty($field['handlers'])){
                        $field['handlers'] = array(''=>LANG_SELECT) + $field['handlers'];
                        $selected = !empty($config[$type][$name]['handler']) ? $config[$type][$name]['handler'] : '';
                        echo html_select("columns[{$type}][{$name}][handler]", $field['handlers'], $selected, ['class' => 'custom-select form-control-sm']);
                    } else
                    if(!empty($field['handlers_only'])){
                        echo $field['handlers_only'];
                    } else {
                        echo ' &mdash;';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
<?php } ?>
        <div class="buttons">
            <?php echo html_submit(LANG_APPLY); ?>
            <?php echo html_button(LANG_CP_GRID_COLYMNS_RESET, 'reset', 'return contentGridColumnsResetSettings()'); ?>
        </div>
    </form>
</div>