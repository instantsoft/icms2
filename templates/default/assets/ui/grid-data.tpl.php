<?php $perpage = !empty($filter['perpage']) ? (int)$filter['perpage'] : $options['perpage']; ?>

<form id="datagrid_filter">
    <?php if ($options['is_pagination']) { ?>
        <input type="hidden" name="page" value="1" />
        <input type="hidden" name="perpage" value="<?php echo $perpage; ?>" />
    <?php } ?>
    <input type="hidden" name="order_by" value="<?php echo isset($filter['order_by']) ? $filter['order_by'] : $options['order_by']; ?>" />
    <input type="hidden" name="order_to" value="<?php echo isset($filter['order_to']) ? $filter['order_to'] : $options['order_to']; ?>" />
    <?php foreach($columns as $name=>$column){ ?>
        <?php if (isset($column['filter'])){ ?>
            <?php echo html_input('hidden', $name, (isset($filter[$name]) ? $filter[$name] : '')); ?>
        <?php } ?>
    <?php } ?>
    <input type="hidden" id="advanced_filter" name="advanced_filter" value="" />
</form>

<form id="datagrid_form" action="" method="post"></form>

<?php if ($options['is_toolbar'] && $this->isToolbar()){ ?>
    <div class="cp_toolbar">
        <?php $this->toolbar(); ?>
    </div>
<?php } ?>

<div class="datagrid_wrapper">
    <table id="datagrid" class="datagrid <?php if ($options['is_selectable']) { ?>datagrid_selectable<?php } ?>" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <?php foreach($columns as $name=>$column){ ?>
                    <?php if ($name=='id' && !$options['show_id']){ continue; } ?>
                    <th <?php if (isset($column['width'])){ ?>width="<?php echo $column['width']; ?>"<?php } ?> rel="<?php echo $name; ?>" <?php if($options['is_sortable']){ ?>class="sortable"<?php } ?>>
                        <?php echo $column['title']; ?>
                    </th>
                <?php } ?>
                <?php if($actions){ ?>
                    <th width="<?php echo (count($actions) * 30); ?>" class="center" rel="dg_actions">
                        <?php echo LANG_CP_ACTIONS; ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($options['is_filter']){ ?>
            <tr class="filter">
                <?php foreach($columns as $name=>$column){ ?>
                    <td>
                        <?php if (!empty($column['filter']) && $column['filter'] != 'none'){ ?>
                            <?php $filter_attributes = !empty($column['filter_attributes']) ? $column['filter_attributes'] : array(); ?>
                            <?php if(strpos($name, 'date_') === 0){ ?>

                                <?php echo html_datepicker('filter_'.$name, (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'input')), array('minDate'=>date(cmsConfig::get('date_format'), 86400))); ?>

                            <?php } else { ?>
                                <?php if (!empty($column['filter_select'])){ ?>

                                    <?php echo html_select('filter_'.$name, (is_array($column['filter_select']['items']) ? $column['filter_select']['items'] : $column['filter_select']['items']($name)), (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name))); ?>

                                <?php } else { ?>

                                    <?php echo html_input('search', 'filter_'.$name, (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name))); ?>

                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?php if ($actions) { ?>
                    <td>
                        &nbsp;
                    </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="datagrid_loading">
        <div class="loading_overlay"></div>
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
</div>

<?php if ($options['is_pagination'] || $options['is_selectable']){ ?>
<div class="datagrid_navigation">
<?php if ($options['is_pagination']){ ?>
    <div class="datagrid_resize">
        <label>
            <?php echo LANG_PAGES_SHOW_PERPAGE; ?>
            <select>
                <?php
                $perpages = array(15,30,50,100,200);
                foreach($perpages as $p){ ?>
                    <option value="<?php echo $p; ?>"<?php if($p===$perpage){ ?> selected<?php } ?>><?php echo $p; ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
<?php } ?>
<?php if ($options['is_selectable']){ ?>
    <div class="datagrid_select_actions">
        <strong class="shint"><?php echo LANG_GRID_SELECT_HINT; ?></strong>
        <span class="sall"><?php echo LANG_SELECT_ALL; ?></span>
        <span class="sremove"><?php echo LANG_DESELECT_ALL; ?></span>
        <span class="sinvert"><?php echo LANG_INVERT_ALL; ?></span>
    </div>
<?php } ?>
    <div class="datagrid_pagination"></div>
</div>
<?php } ?>

<script>

    <?php echo $this->getLangJS('LANG_LIST_EMPTY', 'LANG_LIST_NONE_SELECTED'); ?>

    icms.datagrid.setOptions({
        url: '<?php echo $source_url; ?>',
        pages_count: 0,
        perpage: <?php echo $perpage; ?>,
        show_id: <?php echo intval($options['show_id']); ?>,
        is_sortable: <?php echo intval($options['is_sortable']); ?>,
        is_filter: <?php echo intval($options['is_filter']); ?>,
        is_draggable: <?php echo intval($options['is_draggable']); ?>,
        drag_save_url: '<?php echo $options['drag_save_url']; ?>',
        is_actions: <?php echo intval($options['is_actions']); ?>,
        is_pagination: <?php echo intval($options['is_pagination']); ?>,
        is_selectable: <?php echo intval($options['is_selectable']); ?>,
        order_by: '<?php echo isset($filter['order_by']) ? $filter['order_by'] : $options['order_by']; ?>',
        order_to: '<?php echo isset($filter['order_to']) ? $filter['order_to'] : $options['order_to']; ?>'
    });

    <?php if ($options['is_auto_init']){ ?>
        $(function(){
            icms.datagrid.init();
        });
    <?php } ?>

</script>