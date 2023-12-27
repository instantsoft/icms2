<?php
$this->addMainTplJSName([
    'vendors/vue/vue.min',
    'datagrid-vue',
]);

$this->addTplCSSName(['datatables']);

if(!empty($page_title)) {
    $this->addBreadcrumb($page_title);
    $this->setPageTitle($page_title);
}
?>
<?php if(!empty($h1_title)) { ?>
    <h1><?php echo $h1_title; ?></h1>
<?php } ?>

<?php if ($grid->options['is_toolbar'] && $this->isToolbar()){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<div id="icms-grid" class="position-relative dataTables_wrapper mb-4">
    <div class="d-flex" v-cloak v-if="hasToolbar">
        <div v-if="options.select_actions">
            <form-select v-model="select_action_key" :params="{items: selectActionsItems}" :disabled="selectedRows.length === 0"></form-select>
        </div>
        <div class="ml-auto" v-if="switchable.columns">
            <form-multiselect use_slot="true" v-model="switchable_columns_names" :params="{items: switchable.columns}">
                <a class="btn btn-light btn-sm" href="#">
                    <?php echo html_svg_icon('solid', 'eye-slash'); ?> <span v-text="switchable.title"></span>
                </a>
            </form-multiselect>
        </div>
    </div>
    <div class="table-responsive" :style="{'overflow-x': tableResponsiveOverflow}">
        <table class="datagrid table table-striped table-bordered dataTable bg-white" :class="{datagrid_selectable: options.is_selectable, 'table-dragged': options.is_draggable}">
            <thead>
                <tr>
                    <th class="skeleton" v-cloak v-for="column in columns" :key="column.name" :class="columnClass(column)" :width="column.width" :rel="column.name" @click="clickHeader(column)">
                        <span>{{column.title}}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="filter table-align-middle" v-if="options.is_filter" key="-2">
                    <th class="p-2 skeleton" v-cloak v-for="column in columns" :key="column.name" :class="filterClass(column)">
                        <component v-if="column.filter" :is="column.filter.component" v-model="filter[column.name]" @applyfilter="applyFilter" :params="column.filter.params" save_delayed="true" @changeoverflow="toggleOverflow">text for v-cloak</component>
                    </th>
                </tr>
                <tr class="empty_tr" v-if="rows.length === 0" key="-1">
                    <td class="skeleton" v-cloak :colspan="columns.length" :class="{'skeleton-loading': !source_url}">
                        <span class="empty"><?php echo LANG_LIST_EMPTY; ?></span>
                    </td>
                </tr>
                <tr v-cloak :class="{selected: (row.selected || row.edited)}" v-for="(row, key) in rows" :key="key" @click="selectRow(row)" @dragstart="dragStart(key, $event)" @dragover.prevent @dragend="dragEnd($event)" @dragenter.prevent="dragEnter(key, $event)" @dragleave="dragLeave($event)" @drop="dragFinish(key, $event)" @mousedown="prepareDragStart($event)" @touchstart="prepareDragStart($event)" @mouseup="cancelDragStart($event)" class="animated-row">
                    <td v-for="(col, index) in row.columns" :key="index" :class="col.class">
                        <component :is="'row-column-'+col.renderer" :col="col" :col_key="index" :row_key="key" v-tooltip="col.tooltip" :title="col.tooltip"></component>
                        <inline-save-form v-if="col.editable" :col="col" :col_key="index" :row_key="key" @changeoverflow="toggleOverflow"></inline-save-form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row" v-if="options.is_pagination || options.is_selectable" v-cloak>
        <div class="col-auto col-lg-5 d-flex" v-if="options.is_pagination">
            <pagination v-model="filter.page" @applyfilter="applyFilter" :is_loading="is_loading" :perpage="filter.perpage" :total="total" lang_first="<?php echo LANG_PAGE_FIRST; ?>" lang_last="<?php echo LANG_PAGE_LAST; ?>"></pagination>
            <div class="dataTables_length datagrid_resize">
                <label>
                    <small class="text-muted mr-2"><?php echo LANG_PAGES_SHOW_PERPAGE; ?></small>
                    <select class="custom-select custom-select-sm form-control form-control-sm" v-model="filter.perpage" @change="applyFilter">
                        <?php
                        $perpages = [15,30,50,100,200,500];
                        foreach($perpages as $p){ ?>
                            <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                        <?php } ?>
                    </select>
                </label>
            </div>
        </div>
        <div class="ml-auto col col-lg-7 text-right" v-if="options.is_selectable">
            <div class="datagrid_navigation datagrid_select_actions">
                <small class="shint text-muted"><?php echo LANG_GRID_SELECT_HINT; ?></small>
                <button type="button" class="ml-2 btn btn-primary btn-sm" v-if="selectedRows.length !== rows.length" @click="selectRows">
                    <?php echo LANG_SELECT_ALL; ?>
                </button>
                <button type="button" class="ml-2 btn btn-warning btn-sm" v-if="selectedRows.length > 0" @click="deSelectRows">
                    <?php echo LANG_DESELECT_ALL; ?>
                </button>
                <button type="button" class="ml-2 btn btn-secondary btn-sm" v-if="selectedRows.length > 0 && selectedRows.length !== rows.length" @click="invertSelectRows">
                    <?php echo LANG_INVERT_ALL; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_LIST_NONE_SELECTED'); ?>
    icms.datagrid.initApp(<?php echo json_encode($rows, JSON_UNESCAPED_UNICODE); ?>);
</script>
<?php $this->addBottom(ob_get_clean()); ?>