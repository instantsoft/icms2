var icms = icms || {};

icms.datagrid = (function () {

    let self = this;

    this.callback = false;

    this.app = {};

    /**совместимость с шаблонами**/
    this.init = function(){};

    this.initApp = function(data){

        const app = Vue.createApp({
            data() {
                return {...data,
                    switchable_columns_names: [],
                    select_actions_items_map: [],
                    select_action_key: 0,
                    change_overflow: false,
                    allow_drag_start: false
                };
            },
            mounted() {
                for (let key in this.columns) {
                    if(this.switchable.columns[this.columns[key].name]){
                        this.switchable_columns_names.push(this.columns[key].name);
                    }
                }
                let vm = this;
                this.$nextTick(function () {

                    if(vm.source_url && vm.need_load){
                        self.loadRows();
                    }

                    icms.events.run('datagrid_mounted', self.app);
                });
            },
            updated() {
                this.$nextTick(function () {

                    icms.events.run('datagrid_updated', self.app);

                    icms.modal.bind('a.ajax-modal');

                    if (self.callback) { self.callback(); }
                });
            },
            watch: {
                selectedRows: {
                    handler: function (new_value, old_value) {
                        if(new_value.length === 0){
                            this.select_action_key = 0;
                        }
                    }
                },
                select_action_key: {
                    handler: function (new_value, old_value) {
                        if(new_value.length > 0){
                            let action = this.selectActionsReplaced[new_value];
                            if(action.action === 'open'){
                                self.submitAjax(action.url, action.confirm, action.title+' ('+this.selectedRows.length+')');
                            }
                            if(action.action === 'submit'){
                                self.submit(action.url, action.confirm);
                            }
                        }
                    }
                },
                switchable_columns_names: {
                    handler: function (new_value, old_value) {
                        self.loadRows();
                    }
                }
            },
            computed: {
                selectActionsItems: function() {
                    let items = {};
                    for (let key in this.selectActionsReplaced) {
                        items[key] = this.selectActionsReplaced[key].title;
                    }
                    return items;
                },
                selectActionsReplaced: function() {
                    let items = [];
                    for (let key in this.options.select_actions) {
                        let url = this.options.select_actions[key].url;
                        for (let find_key in this.select_actions_items_map) {
                            url = url.replace(new RegExp('\\{'+find_key+'\\}', 'g'), this.select_actions_items_map[find_key]);
                        }
                        if(this.options.select_actions[key].confirm){
                            url = url+'?csrf_token='+icms.forms.getCsrfToken();
                        }
                        items[key] = {...this.options.select_actions[key], url: url};
                    }
                    return items;
                },
                hasToolbar: function() {
                    return this.switchable.columns || this.options.select_actions;
                },
                selectedRows: function() {
                    return this.rows.filter(function(o){
                        return o.selected;
                    });
                },
                isDragging() {
                    return this.dragging_key > -1;
                },
                tableResponsiveOverflow() {
                    return this.change_overflow ? 'visible' : '';
                }
            },
            methods: {
                applyFilter() {
                    self.loadRows();
                },
                prepareDragStart(ev) {
                    if (!this.options.is_draggable) {
                        return;
                    }
                    this.allow_drag_start = ev.target.classList.contains('dragged_handle');
                    if(this.allow_drag_start){
                        ev.target.parentNode.setAttribute('draggable', 'true');
                    }
                },
                cancelDragStart(ev) {
                    ev.target.parentNode.setAttribute('draggable', 'false');
                },
                dragStart(key, ev) {
                    if (!this.allow_drag_start) {
                        ev.preventDefault(); return;
                    }
                    ev.target.classList.add('dragged');
                    ev.dataTransfer.effectAllowed = 'move';
                    ev.dataTransfer.dropEffect = 'move';
                    ev.dataTransfer.setData("text/plain", key);
                    this.dragging_key = key;
                },
                dragEnter(key, ev) {

                    if (key === this.dragging_key) {
                        return;
                    }

                    ev.target.closest('tr').classList.add('dragged_over');
                },
                dragLeave(ev) {
                    ev.target.closest('tr').classList.remove('dragged_over');
                },
                dragEnd(ev) {
                    ev.target.setAttribute('draggable', 'false');
                    ev.target.classList.remove('dragged');
                    this.dragging_key = -1;
                },
                dragFinish(to_key, ev) {
                    const tr = ev.target.closest('tr');
                    tr.classList.remove('dragged_over');
                    if (to_key === this.dragging_key) {
                        return;
                    }
                    tr.classList.add('dragged_end');
                    setTimeout(function(){
                        tr.classList.remove('dragged_end');
                    }, 3000);
                    this.rows.splice(to_key, 0, this.rows.splice(this.dragging_key, 1)[0]);
                    if (this.options.drag_save_url) {
                        self.submitOrdering(this.options.drag_save_url);
                    }
                },
                invertSelectRows (){
                    for (let key in this.rows) {
                        this.rows[key].selected = !this.rows[key].selected;
                    }
                },
                deSelectRows (){
                    for (let key in this.rows) {
                        this.rows[key].selected = false;
                    }
                },
                selectRows (){
                    for (let key in this.rows) {
                        this.rows[key].selected = true;
                    }
                },
                selectRow (row){
                    if (!this.options.is_selectable){
                        return;
                    }
                    row.selected = !row.selected;
                },
                clickHeader (column){

                    if (!column.sortable){
                        return;
                    }

                    let order_to = 'asc';
                    if (this.filter.order_by === column.name){
                        order_to = this.filter.order_to === 'desc' ? 'asc' : 'desc';
                    }

                    this.filter = {...this.filter, order_by: column.name, order_to: order_to};
                    this.applyFilter();
                },
                filterClass (column){
                    return [
                        !is_empty(this.filter[column.name]) ? 'with_filter' : '',
                        column.class
                    ];
                },
                toggleOverflow (){
                    this.change_overflow = !this.change_overflow;
                },
                columnClass (column){
                    if (!column.sortable){
                        return column.class;
                    }
                    return [
                        column.name === this.filter.order_by ? 'sorting_'+this.filter.order_to : '',
                        'sortable sorting',
                        column.class
                    ];
                }
            }
        });

        const saveMixin = {
            methods: {
                save: function(value) {
                    this.$emit('update:modelValue', value);
                    this.$emit('applyfilter');
                }
            }
        };

        const rangeMixin = {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            computed: {
                from: {
                    get() {
                        if (!this.modelValue) { return null; }
                        return this.modelValue.from;
                    },
                    set(value) {
                        const payload = this.modelValue || {};
                        this.save({...payload, from: value});
                    }
                },
                to: {
                    get() {
                        if (!this.modelValue) { return null; }
                        return this.modelValue.to;
                    },
                    set(value) {
                        const payload = this.modelValue || {};
                        this.save({...payload, to: value});
                    }
                }
            }
        };

        app.component('form-checkbox', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `
            <div class="custom-control custom-switch">
                <input type="checkbox" class="form-check-input input-checkbox custom-control-input" :id="'filter_'+params.attributes.name" @click="save(($event.target.checked ? 1 : 0))" :checked="modelValue>0" v-bind="params.attributes">
                <label class="custom-control-label" :for="'filter_'+params.attributes.name" v-if="params.title">{{params.title}}</label>
            </div>
            `
        });

        app.component('form-date-range', {
            mixins: [rangeMixin],
            template: `
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">{{params.lang_from}}</span>
              </div>
              <form-date v-model="from" :params="params" />
              <div class="input-group-append">
                <span class="input-group-text border-right-0">{{params.lang_to}}</span>
              </div>
              <form-date v-model="to" :params="params" />
            </div>
            `
        });

        app.component('form-range', {
            mixins: [rangeMixin],
            template: `
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">{{params.lang_from}}</span>
              </div>
              <form-input v-model="from" :params="params" save_delayed="true" />
              <div class="input-group-append">
                <span class="input-group-text border-right-0">{{params.lang_to}}</span>
              </div>
              <form-input v-model="to" :params="params" save_delayed="true" />
            </div>
            `
        });

        app.component('form-date', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `<input type="date" class="form-control form-control-sm" :value="modelValue" @input="save($event.target.value)" v-bind="params.attributes">`
        });

        app.component('form-time', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `<input type="time" class="form-control form-control-sm" :value="modelValue" @input="save($event.target.value)" v-bind="params.attributes">`
        });

        app.component('form-datetime', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `<input type="datetime-local" class="form-control form-control-sm" :value="modelValue" @input="save($event.target.value)" v-bind="params.attributes">`
        });

        app.component('form-input', {
            props: ['params', 'modelValue', 'save_delayed'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            methods: {
                debounce: function(fn, delay) {
                    let id = null;
                    return function () {
                        clearTimeout(id);
                        let args = arguments;
                        let that = this;
                        id = setTimeout(function () {
                            fn.apply(that, args);
                        }, delay);
                    };
                }
            },
            computed: {
                saveDelayed: function() {
                    if(this.save_delayed){
                        return this.debounce(this.save, 500);
                    }
                    return this.save;
                }
            },
            template: `<input autocomplete="off" type="text" class="input form-control form-control-sm" :value="modelValue" @input="saveDelayed($event.target.value)" v-bind="params.attributes ? params.attributes : {}">`
        });

        app.component('form-select', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `<select class="form-control custom-select custom-select-sm" :value="modelValue" @input="save($event.target.value)" v-bind="params.attributes ? params.attributes : {}">
                <option v-for="(title, value) in params.items" :value="value">{{title}}</option>
            </select>`
        });

        app.component('form-textarea', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            template: `<textarea class="form-control" :value="modelValue" @input="save($event.target.value)" v-bind="params.attributes ? params.attributes : {}"></textarea>`
        });

        app.component('form-filter', {
            props: ['params', 'modelValue'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            methods: {
                open: function() {
                    icms.modal.openAjax(this.params.href,{},false, this.params.lang_filter);
                },
                cancel: function() {
                    this.save('');
                }
            },
            template: `
                <div class="text-center">
                    <a v-if="!modelValue" href="#" @click.prevent="open" class="btn btn-link text-decoration-none btn-sm">
                        <span v-html="params.icon_filter"></span> {{params.lang_filter}}
                    </a>
                    <a v-if="modelValue" href="#" @click.prevent="cancel" class="btn btn-link text-white text-decoration-none btn-sm">
                        <span v-html="params.icon_cancel"></span> {{params.lang_cancel}}
                    </a>
                </div>
            `
        });

        app.component('form-multiselect', {
            props: {
                params: {
                    default: {items: [], attributes: {}}
                },
                modelValue: {
                    default: []
                },
                use_slot: {
                    default: false
                }
            },
            mixins: [saveMixin],
            emits: ['update:modelValue', 'changeoverflow', 'applyfilter'],
            data() {
                return {
                    is_show: false
                };
            },
            computed: {
                selectedTitles: function() {
                    let titles = [];
                    for (let key in this.selected) {
                        titles.push(this.params.items[this.selected[key]]);
                    }
                    return titles.join(', ');
                },
                selected: {
                    get() {
                        if (!this.modelValue) { return []; }
                        return this.modelValue;
                    },
                    set(value) {
                        this.save(value);
                    }
                }
            },
            methods: {
                toggle: function() {
                    this.$emit('changeoverflow');
                    this.is_show = !this.is_show;
                },
                close: function() {
                    if(this.is_show){
                        this.$emit('changeoverflow');
                    }
                    this.is_show = false;
                }
            },
            template: `
            <div class="dropdown dropdown-multiselect" v-clickaway="close">
                <div v-if="use_slot" @click.prevent="toggle">
                    <slot></slot>
                </div>
                <input v-if="!use_slot" class="input form-control form-control-sm" v-model="selectedTitles" type="text" readonly="true" @click="toggle" v-bind="params.attributes">
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left shadow px-2 pt-2 pb-0" :class="{show_menu: is_show}">
                    <div class="custom-control custom-checkbox pb-2" v-for="(title, index) in params.items" :key="index">
                        <input class="custom-control-input" type="checkbox" :id="'dgselect-'+index" :value="index" v-model="selected">
                        <label class="custom-control-label" :for="'dgselect-'+index">
                            {{title}}
                        </label>
                    </div>
                </div>
            </div>
            `
        });

        app.component('row-column-basic', {
            props: ['col', 'col_key', 'row_key'],
            template: `
                <span class="datagrid-column-basic">
                    <a v-if="col.href" :href="col.href">{{col.value}}</a>
                    <span v-if="!col.href">{{col.value}}</span>
                </span>
            `
        });

        app.component('row-column-flag', {
            props: ['col', 'col_key', 'row_key'],
            data() {
                return {
                    is_loading: false
                };
            },
            computed: {
                isHref (){
                    if(!this.col.confirm){
                        return this.col.href ? true : false;
                    }
                    return this.col.href && this.col.value === 0;
                },
                flagClass (){
                    return [
                        this.is_loading ? 'loading' : '',
                        (this.col.value > 0 ? this.col.flag_class+'_on' : (this.col.value < 0 ? this.col.flag_class+'_middle' : this.col.flag_class+'_off')),
                        this.col.flag_class,
                        'flag_trigger'
                    ];
                }
            },
            methods: {
                toggle: function() {
                    if(this.col.confirm && !confirm(this.col.confirm)){
                        return;
                    }
                    let vm = this;
                    this.is_loading = true;
                    self.ajax(this.col.href, {}, function(result){

                        vm.is_loading = false;

                        if (result.error){ return; }

                        vm.$root.rows[vm.row_key].columns[vm.col_key].value = +result.is_on;
                    });
                }
            },
            template: `
            <div :class="flagClass"><a v-if="isHref" :href="col.href" @click.stop.prevent="toggle"></a></div>
            `
        });

        app.component('row-column-html', {
            props: ['col', 'col_key', 'row_key'],
            template: `
            <div class="datagrid-column-html" :class="{'datagrid-column-html__link': (col.href || col.editable)}">
                <a v-if="col.href" :href="col.href">
                    <span v-html="col.value"></span>
                </a>
                <div class="datagrid-column-html__wraper" v-if="!col.href" v-html="col.value"></div>
            </div>
            `
        });

        app.component('inline-save-form', {
            props: ['col', 'col_key', 'row_key'],
            emits: ['changeoverflow'],
            data() {
                return {
                    show_form: false,
                    is_busy: false
                };
            },
            methods: {
                save: function() {
                    let vm = this;
                    this.is_busy = true;
                    self.ajax(this.col.editable.save_action, {value: (this.col.editable.value ? this.col.editable.value : ''), save_row_field: 1}, function(result){
                        vm.is_busy = false;
                        if (result.error) {
                            self.alert(result.error);
                        } else {
                            vm.hideFrom();
                            vm.$root.rows[vm.row_key] = result.row;
                        }
                    });
                },
                hideFrom: function() {
                    this.$emit('changeoverflow');
                    this.show_form = false;
                    this.$root.rows[this.row_key].edited = false;
                },
                showFrom: function() {
                    this.$emit('changeoverflow');
                    this.show_form = true;
                    this.$root.rows[this.row_key].edited = true;
                }
            },
            template: `
            <a class="ml-2 d-inline-block datagrid-editable__link" href="#" v-html="col.editable.edit_icon" :title="col.editable.lang_edit" @click.prevent.stop="showFrom"></a>
            <teleport to="#icms-grid">
                <div class="datagrid-backdrop" v-if="show_form"></div>
            </teleport>
            <div class="grid_field_edit edit_by_click d-block" v-if="show_form" v-clickaway="hideFrom">
                <component :is="col.editable.component" v-focus @keyup.esc="hideFrom" @keyup.enter="save" v-model="col.editable.value" :params="col.editable"></component>
                <button class="button btn inline_submit btn-primary" type="button" @click="save" :disabled="is_busy" :class="{'is-busy': is_busy}">
                    <span>{{col.editable.lang_save}}</span>
                </button>
            </div>
            `
        });

        app.component('row-column-actions', {
            props: ['col', 'col_key', 'row_key'],
            methods: {
                confirm: function(confirm_text, event) {
                    if(!confirm_text){
                        return true;
                    }
                    if(!confirm(confirm_text)){
                        event.preventDefault();
                    }
                }
            },
            template: `
            <div class="actions" v-if="col.value.length > 0">
                <a v-for="action in col.value" v-tooltip :title="action.title" :target="action.target" :class="action.class" @click.stop="confirm(action.confirm, $event)" :href="action.href">
                    <span v-if="action.icon" v-html="action.icon"></span>
                </a>
            </div>
            `
        });

        app.component('pagination-item', {
            props: ['modelValue', 'title', 'page', 'is_loading'],
            inject: ['setPage'],
            data() {
                return {
                    is_busy: false
                };
            },
            updated() {
                if(this.is_busy){
                    this.is_busy = this.is_loading;
                }
            },
            methods: {
                goPage: function(page) {
                    this.is_busy = true;
                    this.setPage(page);
                }
            },
            template: `
            <li class="page-item">
                <a class="page-link" :class="{'is-busy': is_busy}" href="#" @click.prevent="goPage(page)">
                    <span>{{title}}</span>
                </a>
            </li>
            `
        });

        app.component('pagination', {
            props: ['modelValue', 'total', 'perpage', 'lang_first', 'lang_last', 'is_loading'],
            emits: ['update:modelValue', 'applyfilter'],
            mixins: [saveMixin],
            data() {
                return {
                    max_show_pages: 3,
                    page_count: 0
                };
            },
            provide() {
                return {
                    setPage: this.setPage
                };
            },
            mounted() {
                this.setPageCount();
            },
            updated() {
                this.setPageCount();
            },
            computed: {
                prevPage (){
                    let page = +this.modelValue - 1;
                    return page < 1 ? 1 : page;
                },
                nextPage (){
                    let page = +this.modelValue + 1;
                    return page > this.page_count ? this.page_count : page;
                },
                pages (){
                    let start_page = this.modelValue - Math.trunc(this.max_show_pages / 2);
                    if(start_page < 1){
                        start_page = 1;
                    }
                    if(start_page < 1){
                        start_page = 1;
                    }
                    let end_page = this.max_show_pages+start_page;
                    if(end_page > (this.page_count+1)){
                        end_page = this.page_count+1;
                        if(start_page > 1){
                            start_page -= 1;
                        }
                    }
                    let pages = [];
                    for (let i = start_page; i < end_page; i++) {
                        pages.push(i);
                    }
                    return pages;
                }
            },
            methods: {
                setPageCount: function() {
                    this.page_count = Math.ceil(this.total/this.perpage);
                    if(this.modelValue > this.page_count && this.page_count > 0){
                        this.setPage(this.page_count);
                    }
                },
                setPage: function(page) {
                    this.save(page);
                }
            },
            template: `
            <div v-if="total > perpage" class="datagrid_pagination mr-2">
                <ul class="pagination pagination-sm justify-content-start m-0">
                    <pagination-item :class="{disabled: modelValue == 1}" :is_loading="is_loading" :title="lang_first" page="1"></pagination-item>
                    <pagination-item :class="{disabled: modelValue == 1}" :is_loading="is_loading" title="&larr;" :page="prevPage"></pagination-item>
                    <pagination-item v-for="page in pages" :class="{active: modelValue == page}" :is_loading="is_loading" :title="page" :page="page"></pagination-item>
                    <pagination-item :class="{disabled: modelValue == page_count}" :is_loading="is_loading" title="&rarr;" :page="nextPage"></pagination-item>
                    <pagination-item :class="{disabled: modelValue == page_count}" :is_loading="is_loading" :title="lang_last" :page="page_count"></pagination-item>
                </ul>
            </div>
            `
        });

        app.directive('tooltip', function (el, binding) {
            if(typeof binding.value === 'undefined' || binding.value){
                new bootstrap.Tooltip(el, {placement: 'top', trigger : 'hover'});
            }
        });

        app.directive('focus', {
            mounted(el) {
                el.focus();
            }
        });

        app.directive('clickaway', {
            mounted(el, binding) {
                let callback = binding.value;
                if (typeof callback !== 'function') {
                    return;
                }
                let initialmacrotaskended = false;
                setTimeout(function() {
                    initialmacrotaskended = true;
                }, 0);
                el.clickaway_handler = function(ev) {
                    let path = ev.path || (ev.composedPath ? ev.composedPath() : undefined);
                    if (initialmacrotaskended && (path ? path.indexOf(el) < 0 : !el.contains(ev.target))) {
                        return callback.call(ev);
                    }
                };
                document.documentElement.addEventListener('click', el.clickaway_handler, false);
            },
            unmounted(el) {
                document.documentElement.removeEventListener('click', el.clickaway_handler, false);
                delete el.clickaway_handler;
            }
        });

        self.app = app.mount('#icms-grid');
    };

    this.setURL = function(url){
        self.app.source_url = url;
        return self.setPage(1);
    };

    this.setPage = function (page, perpage) {
        self.app.filter.page = page;
        if (typeof perpage !== 'undefined') {
            self.app.filter.perpage = +perpage;
        }
    };

    this.applyAdvancedFilter = function(form){
        self.app.filter = {...self.app.filter, advanced_filter: new URLSearchParams(new FormData(form)).toString(), page: 1};
        self.loadRows();
        icms.modal.close();
        return false;
    };

    this.submitOrdering = function(url){
        let items = [];
        for(let key in self.app.rows) {
            if (self.app.rows.hasOwnProperty(key)) {
                items.push(self.app.rows[key].id);
            }
        }
        self.ajax(url, {items: items}, function(result){
            if(typeof (toastr) !== 'undefined'){
                toastr.success(result.success_text);
            }
        });
        return false;
    };

    this.submitAjax = function (url, confirm_message, title){

        let selected = self.app.selectedRows;

        if(selected.length === 0){
            self.alert(LANG_LIST_NONE_SELECTED);
            return;
        }

        if (typeof (confirm_message) === 'string') {
            if (!confirm(confirm_message)) {
                icms.datagrid.app.select_action_key = 0;
                return false;
            }
        }

        /** @todo убрать jquery */
        if (typeof(url) !== 'string') {
            title = $(url).attr('title');
            url = $(url).data('url');
        }

        let items = [];

        for(let key in selected) {
            if (selected.hasOwnProperty(key)) {
                items.push(selected[key].id);
            }
        }

        icms.modal.openAjax(url, {selected: items}, function(){
            icms.datagrid.app.select_action_key = 0;
        }, title);

        return false;
    };

    this.submit = function(url, confirm_message){

        let selected = self.app.selectedRows;

        if(selected.length === 0){
            self.alert(LANG_LIST_NONE_SELECTED);
            return;
        }

        if (typeof (confirm_message) === 'string') {
            if (!confirm(confirm_message)) {
                icms.datagrid.app.select_action_key = 0;
                return false;
            }
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        for(let key in selected) {
            if (selected.hasOwnProperty(key)) {
                const hiddenField = document.createElement('input');
                hiddenField.type  = 'hidden';
                hiddenField.name  = 'selected[]';
                hiddenField.value = selected[key].id;
                form.appendChild(hiddenField);
            }
        }
        document.body.appendChild(form);
        form.submit();

        return;
    };

    this.loadRows = function (callback){

        if(!self.app.source_url){ return; }

        self.app.is_loading = true;

        self.ajax(self.app.source_url, {filter: http_build_query(self.app.filter), visible_columns: self.app.switchable_columns_names}, function(result){

            for(let key in result) {
                if(result.hasOwnProperty(key)){
                    if (typeof(self[key]) === 'function') {
                        setTimeout(function () {
                            self[key](result[key]);
                        }, 0);
                    } else {
                        self.app[key] = result[key];
                    }
                }
            }

            icms.events.run('datagrid_rows_loaded', result);

            if (typeof(callback) !== 'undefined'){
                callback(result);
            }
        });
    };

    this.alert = function (text){
        if(typeof (toastr) === 'undefined'){
            alert(text);
        } else {
            toastr.warning(text);
        }
    };

    this.ajax = function (url, params, callback){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.responseType = 'json';
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {

            if (xhr.status !== 200) {
                self.alert(`Error ${xhr.status}: ${xhr.statusText}`);
                return;
            }

            if (typeof(callback) !== 'undefined'){
                callback(xhr.response);
            }
        };
        xhr.onerror = function() {
            self.alert('Load error');
        };
        let form_data = null;
        if (typeof(params) === 'object'){
            form_data = new FormData();
            for(let key in params) {
                if(params.hasOwnProperty(key)){
                    if(Array.isArray(params[key])){
                        if(params[key].length > 0){
                            for(let key_arr in params[key]) {
                                form_data.append(key+'[]', params[key][key_arr]);
                            }
                        } else {
                            form_data.append(key+'[]', '');
                        }
                    } else {
                        form_data.append(key, params[key]);
                    }
                }
            }
        }
        xhr.send(form_data);
    };

    return this;
}).call(icms.datagrid || {});

function is_empty(e) {
    if(Array.isArray(e)){
        return e.length === 0;
    }
    if(typeof e === 'object'){
        for(let prop in e) {
            if(!is_empty(e[prop])) {
                return false;
            }
        }
        return true;
    }
    switch (e) {
        case "":
        case 0:
        case "0":
        case null:
        case false:
        case undefined:
            return true;
            default:
            return false;
    }
}

function urlencode(str) {
    str = (str + '');
    return encodeURIComponent(str)
            .replace(/!/g, '%21')
            .replace(/'/g, '%27')
            .replace(/\(/g, '%28')
            .replace(/\)/g, '%29')
            .replace(/\*/g, '%2A')
            .replace(/~/g, '%7E')
            .replace(/%20/g, '+');
}

function http_build_query(formdata, numericPrefix, argSeparator) {

    let value;
    let key;
    const tmp = [];

    var _httpBuildQueryHelper = function (key, val, argSeparator) {
        let k;
        const tmp = [];
        if (val === true) {
            val = '1';
        } else if (val === false) {
            val = '0';
        }
        if (val !== null) {
            if (typeof val === 'object') {
                for (k in val) {
                    if (val[k] !== null) {
                        tmp.push(_httpBuildQueryHelper(key + '[' + k + ']', val[k], argSeparator));
                    }
                }
                return tmp.join(argSeparator);
            } else if (typeof val !== 'function') {
                return urlencode(key) + '=' + urlencode(val);
            } else {
                return '';
            }
        } else {
            return '';
        }
    };

    if (!argSeparator) {
        argSeparator = '&';
    }
    for (key in formdata) {
        value = formdata[key];
        if (numericPrefix && !isNaN(key)) {
            key = String(numericPrefix) + key;
        }
        const query = _httpBuildQueryHelper(key, value, argSeparator);
        if (query !== '') {
            tmp.push(query);
        }
    }
    return tmp.join(argSeparator);
}
