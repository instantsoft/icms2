<div class="modal_padding datagrid_grid_columns_settings">

    <form onsubmit="return contentGridColumnsSettings()" action="<?php echo $this->href_to('content', array('grid_columns', $ctype_id)); ?>">
<?php foreach($items as $type => $fields){ ?>
        <fieldset>
            <legend><?php echo constant('LANG_CP_GRID_COLYMNS_'.strtoupper($type)); ?></legend>
            <table>
                <tr>
                    <th><?php echo LANG_TITLE; ?></th>
                    <th><?php echo LANG_CP_GRID_COLYMNS_FILTER; ?></th>
                    <th><?php echo LANG_CP_GRID_COLYMNS_DISPLAY; ?></th>
                </tr>
                <?php foreach($fields as $name => $field){ ?>
                    <tr>
                        <td>
                            <label>
                            <?php echo html_checkbox('columns['.$type.']['.$name.'][enabled]', !empty($config[$type][$name]['enabled'])); ?>
                            <?php html($field['title']); ?>
                            </label>
                            <?php echo html_input('hidden', "columns[{$type}][{$name}][field]", $name); ?>
                        </td>
                        <td>
                            <?php
                            if(!empty($field['filters'])){
                                $field['filters'] = array(''=>LANG_SELECT) + $field['filters'];
                                $selected = !empty($config[$type][$name]['filter']) ? $config[$type][$name]['filter'] : '';
                                echo html_select("columns[{$type}][{$name}][filter]", $field['filters'], $selected);
                            } else {
                                echo '&mdash;';
                            } ?>
                        </td>
                        <td>
                            <?php
                            if(!empty($field['handlers'])){
                                $field['handlers'] = array(''=>LANG_SELECT) + $field['handlers'];
                                $selected = !empty($config[$type][$name]['handler']) ? $config[$type][$name]['handler'] : '';
                                echo html_select("columns[{$type}][{$name}][handler]", $field['handlers'], $selected);
                            } else
                            if(!empty($field['handlers_only'])){
                                echo $field['handlers_only'];
                            } else {
                                echo '&mdash;';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </fieldset>
<?php } ?>
        <div class="buttons">
            <?php echo html_submit(LANG_APPLY); ?>
            <?php echo html_button(LANG_CP_GRID_COLYMNS_RESET, 'reset', 'return contentGridColumnsResetSettings()'); ?>
        </div>

    </form>

</div>