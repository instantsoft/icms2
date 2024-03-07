<?php

class modelContent extends cmsModel {

    use icms\traits\controllers\models\fieldable;

    protected $pub_filter_disabled = false;
    protected $pub_filtered = false;

    protected static $all_ctypes = null;

    public function __construct() {

        parent::__construct();

        $this->loadAllCtypes();
    }

//============================================================================//
//=======================    ТИПЫ КОНТЕНТА   =================================//
//============================================================================//

    public function getContentTypesCount() {

        if (!self::$all_ctypes) {
            return 0;
        }

        return count(self::$all_ctypes);
    }

    public function loadAllCtypes() {
        return !isset(self::$all_ctypes) ? $this->reloadAllCtypes() : $this;
    }

    public function reloadAllCtypes($enabled = true) {

        if ($enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        self::$all_ctypes = $this->getContentTypesFiltered();
        return $this;
    }

    public function getContentTypes() {
        return self::$all_ctypes;
    }

    protected function contentTypesCallback($item, $model) {

        $item['options'] = self::yamlToArray($item['options']);
        $item['labels']  = $model->makeContentTypeLabels($item['labels']);

        // YAML некорректно преобразовывает пустые значения массива
        // убрать после перевода всего на JSON
        if (!empty($item['options']['list_style'])) {
            if (is_array($item['options']['list_style'])) {
                $list_styles = [];
                foreach ($item['options']['list_style'] as $key => $value) {
                    $list_styles[$key] = is_array($value) ? '' : $value;
                }
                $item['options']['list_style'] = $list_styles;
            }
        }
        if (!empty($item['options']['context_list_cover_sizes'])) {
            if (is_array($item['options']['context_list_cover_sizes'])) {
                $list_styles = [];
                foreach ($item['options']['context_list_cover_sizes'] as $key => $value) {
                    $list_styles[$key ? $key : ''] = $value;
                }
                $item['options']['context_list_cover_sizes'] = $list_styles;
            }
        }

        return $item;
    }

    public function getContentTypesFiltered() {

        $this->useCache('content.types');

        if (!$this->order_by) {
            $this->orderBy('ordering');
        }

        return $this->get('content_types', [$this, 'contentTypesCallback']) ?: [];
    }

    private function makeContentTypeLabels($labels) {

        $labels = self::yamlToArray($labels);
        if (empty($labels['one_accusative'])) {
            $labels['one_accusative'] = $labels['one'];
        }
        if (empty($labels['two_accusative'])) {
            $labels['two_accusative'] = $labels['two'];
        }
        if (empty($labels['many_accusative'])) {
            $labels['many_accusative'] = $labels['many'];
        }
        if (empty($labels['one_genitive'])) {
            $labels['one_genitive'] = $labels['one'];
        }
        if (empty($labels['two_genitive'])) {
            $labels['two_genitive'] = $labels['two'];
        }
        if (empty($labels['many_genitive'])) {
            $labels['many_genitive'] = $labels['many'];
        }

        return $labels;
    }

    public function getContentTypesCountFiltered() {
        return $this->getCount('content_types');
    }

    public function getContentTypesNames() {

        if (!self::$all_ctypes) {
            return false;
        }

        foreach (self::$all_ctypes as $ctype_id => $ctype) {
            $names[$ctype_id] = $ctype['name'];
        }

        return $names;
    }

    public function getContentType($id, $by_field = 'id') {

        if (!self::$all_ctypes) {
            return false;
        }

        foreach (self::$all_ctypes as $ctype_id => $ctype) {
            if ($ctype[$by_field] == $id) {
                return $ctype;
            }
        }

        return false;
    }

    public function getContentTypeByName($name) {
        return $this->getContentType($name, 'name');
    }

//============================================================================//
//======================    ПАПКИ КОНТЕНТА   =================================//
//============================================================================//

    public function addContentFolder($ctype_id, $user_id, $title) {
        return $this->insert('content_folders', [
            'ctype_id' => $ctype_id,
            'user_id'  => $user_id,
            'title'    => $title
        ]);
    }

    public function getContentFolders($ctype_id, $user_id) {
        return $this->filterEqual('ctype_id', $ctype_id)->
                filterEqual('user_id', $user_id)->
                get('content_folders');
    }

    public function getContentFolder($id) {
        return $this->joinUserLeft()->getItemById('content_folders', $id, function($item, $model){
            $item['user'] = array(
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => $item['user_avatar']
            );
            return $item;
        });
    }

    public function getContentFolderByTitle($title, $ctype_id, $user_id) {
        return $this->filterEqual('ctype_id', $ctype_id)->
                filterEqual('user_id', $user_id)->
                getItemByField('content_folders', 'title', $title);
    }

    public function updateContentFolder($id, $folder) {
        return $this->update('content_folders', $id, $folder);
    }

    public function deleteContentFolder($folder, $is_delete_content = true) {

        $ctype = $this->getContentType($folder['ctype_id']);

        $this->filterEqual('folder_id', $folder['id']);

        if (!$is_delete_content) {

            $this->updateFiltered($this->getContentTypeTableName($ctype['name']), [
                'folder_id' => null
            ]);
        }

        if ($is_delete_content) {

            $this->disableDeleteFilter()->disableApprovedFilter()->
                    disablePubFilter()->disablePrivacyFilter();

            $items = $this->getContentItems($ctype['name']);

            if ($items) {
                foreach ($items as $item) {
                    $this->deleteContentItem($ctype['name'], $item['id']);
                }
            }
        }

        return $this->delete('content_folders', $folder['id']);
    }

//============================================================================//
//============================    СВОЙСТВА   =================================//
//============================================================================//

    public function getContentProps($ctype_name, $category_id = false) {

        $props_table_name = $this->getContentTypeTableName($ctype_name, '_props');
        $bind_table_name  = $this->getContentTypeTableName($ctype_name, '_props_bind');

        if ($category_id) {
            $this->selectOnly('p.*');
            $this->select('c.title', 'cat_title');
            $this->select('i.cat_id');
            $this->join($props_table_name, 'p', 'p.id = i.prop_id');
            $this->join($this->getContentCategoryTableName($ctype_name), 'c', 'c.id = i.cat_id');
            if (is_array($category_id)) {
                $this->filterIn('cat_id', $category_id);
            } else {
                $this->filterEqual('cat_id', $category_id);
            }
            $this->orderBy('ordering');
            $table_name = $bind_table_name;
        } else {
            $table_name = $props_table_name;
        }

        return $this->get($table_name, function ($item, $model) {
            $item['options'] = self::yamlToArray($item['options']);
            return $item;
        });
    }

    public function unbindContentProps($ctype_name, $cat_id) {

        return $this->filterEqual('cat_id', $cat_id)->
            deleteFiltered($this->getContentTypeTableName($ctype_name, '_props_bind'));
    }

    public function getPropsValues($ctype_name, $item_id) {

        return $this->filterEqual('item_id', $item_id)->
                get($this->getContentTypeTableName($ctype_name, '_props_values'),
                function ($item, $model) {
                    return $item['value'];
                }, 'prop_id');
    }

    public function addPropsValues($ctype_name, $item_id, $props_values) {

        $table_name = $this->getContentTypeTableName($ctype_name, '_props_values');

        foreach ($props_values as $prop_id => $value) {
            $this->insert($table_name, [
                'prop_id' => $prop_id,
                'item_id' => $item_id,
                'value'   => $value
            ]);
        }

        return true;
    }

    public function updatePropsValues($ctype_name, $item_id, $props_values) {

        $props_ids = array_keys($props_values);

        $this->filterEqual('item_id', $item_id)->
            filterIn('prop_id', $props_ids)->
            deleteFiltered($this->getContentTypeTableName($ctype_name, '_props_values'));

        return $this->addPropsValues($ctype_name, $item_id, $props_values);
    }

    public function deletePropsValues($ctype_name, $item_id) {

        return $this->filterEqual('item_id', $item_id)->
            deleteFiltered($this->getContentTypeTableName($ctype_name, '_props_values'));
    }

//============================================================================//
//===============================   СВЯЗИ   ==================================//
//============================================================================//

    public function getContentRelationByTypes($ctype_id, $child_ctype_id, $target_controller = 'content') {

        $this->filterEqual('ctype_id', $ctype_id);
        $this->filterEqual('child_ctype_id', $child_ctype_id);
        $this->filterEqual('target_controller', $target_controller);

        return $this->getItem('content_relations', function ($item) {
            $item['options'] = self::yamlToArray($item['options']);
            return $item;
        });
    }

    public function getContentTypeChilds($ctype_id) {

        $this->useCache('content.relations');

        $this->selectOnly('i.*');
        $this->selectTranslatedField('c.title', 'content_types', 'child_title');
        $this->select('c.labels', 'child_labels');
        $this->select('c.name', 'child_ctype_name');

        $this->joinLeft('content_types', 'c', "c.id = i.child_ctype_id AND i.target_controller = 'content'");

        $this->filterEqual('ctype_id', $ctype_id);

        $this->orderBy('ordering', 'asc');

        return $this->get('content_relations', function ($item, $model) {

            $item['child_labels'] = self::yamlToArray($item['child_labels']);
            $item['options']      = self::yamlToArray($item['options']);
            if (empty($item['child_ctype_name'])) {
                $item['child_ctype_name'] = $item['target_controller'];
            }

            return $item;
        });
    }

    public function getContentTypeParents($ctype_id, $target_controller = 'content') {

        $this->selectOnly('i.*');
        $this->select('c.name', 'ctype_name');
        $this->select('c.title', 'ctype_title');
        $this->select('c.id', 'ctype_id');

        $this->joinLeft('content_types', 'c', 'c.id = i.ctype_id');

        $this->filterEqual('child_ctype_id', $ctype_id);
        $this->filterEqual('target_controller', $target_controller);

        $this->orderBy('ordering', 'asc');

        $parents = $this->get('content_relations');
        if (!$parents) { return []; }

        foreach ($parents as $id => $parent) {
            $parents[$id]['id_param_name'] = 'parent_' . $parent['ctype_name'] . '_id';
        }

        return $parents;
    }

    public function updateChildItemParentIds($relation) {

        $this->selectOnly('parent_item_id', 'id');

        $this->filterEqual('parent_ctype_id', $relation['parent_ctype_id']);
        $this->filterEqual('child_ctype_id', $relation['child_ctype_id']);
        $this->filterEqual('child_item_id', $relation['child_item_id']);
        $this->filterEqual('target_controller', (isset($relation['target_controller']) ? $relation['target_controller'] : 'content'));

        $parent_items_ids = $this->get('content_relations_bind', function ($item, $model) {
            return $item['id'];
        }, false);

        $success = false;

        if ($parent_items_ids) {

            $ids = trim(implode(',', $parent_items_ids));

            if ($ids) {

                $item_table = $this->getContentTypeTableName($relation['child_ctype_name']);

                if ($item_table === 'users') { $item_table = '{users}'; }

                $success = $this->update($item_table, $relation['child_item_id'], [
                    'parent_' . $relation['parent_ctype_name'] . '_id' => $ids
                ]);
            }
        }

        cmsCache::getInstance()->clean('content.list.' . $relation['child_ctype_name']);
        cmsCache::getInstance()->clean('content.item.' . $relation['child_ctype_name']);
        cmsCache::getInstance()->clean('content.list.' . $relation['parent_ctype_name']);
        cmsCache::getInstance()->clean('content.item.' . $relation['parent_ctype_name']);

        return $success;
    }

    public function bindContentItemRelation($relation) {

        $id = $this->insert('content_relations_bind', $relation);

        $this->updateChildItemParentIds($relation);

        return $id;
    }

    public function unbindContentItemRelation($relation) {

        $this->filterEqual('parent_ctype_id', $relation['parent_ctype_id']);
        $this->filterEqual('parent_item_id', $relation['parent_item_id']);
        $this->filterEqual('child_ctype_id', $relation['child_ctype_id']);
        $this->filterEqual('child_item_id', $relation['child_item_id']);
        $this->filterEqual('target_controller', (isset($relation['target_controller']) ? $relation['target_controller'] : 'content'));

        $this->deleteFiltered('content_relations_bind');

        return $this->updateChildItemParentIds($relation);
    }

    public function deleteContentItemRelations($ctype_id, $item_id) {

        $this->filterEqual('child_ctype_id', $ctype_id);
        $this->filterEqual('child_item_id', $item_id);

        return $this->deleteFiltered('content_relations_bind');
    }

    public function getContentItemParents($parent_ctype, $child_ctype, $item_id) {

        if (empty($child_ctype['controller'])) {
            $child_ctype['controller'] = 'content';
        }

        $this->selectOnly('i.*');

        $parent_ctype_table = $this->getContentTypeTableName($parent_ctype['name']);

        if ($parent_ctype_table === 'users') {
            $parent_ctype_table = '{users}';
        }

        $join_on = "r.parent_ctype_id = '{$parent_ctype['id']}' AND " .
                'r.child_ctype_id ' . (!empty($child_ctype['id']) ? '=' . $child_ctype['id'] : 'IS NULL' ) . ' AND ' .
                "r.child_item_id = '{$item_id}' AND " .
                "r.parent_item_id = i.id AND r.target_controller = '{$child_ctype['controller']}'";

        $this->joinInner('content_relations_bind', 'r', $join_on);

        return $this->get($parent_ctype_table);
    }

//============================================================================//
//=============================   Фильтры   ==================================//
//============================================================================//

    public function getContentFilters($ctype_name){

        $this->useCache('content.filters.'.$ctype_name);

        $table_name = $this->getContentTypeTableName($ctype_name, '_filters');

        return $this->get($table_name, function($item, $model){

            $item['cats'] = self::stringToArray($item['cats']);

            if(isset($item['filters'])){
                $item['filters'] = self::stringToArray($item['filters']);
            }

            return $item;
        });
    }

    public function getContentFilter($ctype, $id, $by_hash = false, $cat_id = 0) {

        if (!$this->isFiltersTableExists($ctype['name'])) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name'], '_filters');

        $this->useCache('content.filters.' . $ctype['name']);

        $field_name = 'id';
        if (!is_numeric($id)) {
            if ($by_hash) {
                $field_name = 'hash';
            } else {
                $field_name = 'slug';
            }
        }

        $this->filterEqual($field_name, $id);

        $item = $this->getItem($table_name, function ($item, $model) use ($ctype) {

            $item['cats']       = self::stringToArray($item['cats']);
            $item['filters']    = self::stringToArray($item['filters']);
            $item['ctype_name'] = $ctype['name'];

            return $item;
        });

        if($item && $cat_id && $item['cats'] && !in_array($cat_id, $item['cats'])){
            $item = false;
        }

        return $item;
    }

//============================================================================//
//==============================   НАБОРЫ   ==================================//
//============================================================================//

    public function getContentDatasets($ctype_id = false, $only_visible = false, $item_callback = false) {

        if ($ctype_id) {
            if (is_numeric($ctype_id)) {
                $this->filterEqual('ctype_id', $ctype_id);
            } else {
                $this->filterEqual('target_controller', $ctype_id);
            }
        }

        if ($only_visible) {
            $this->filterEqual('is_visible', 1);
        }

        $this->orderBy('ordering');

        $this->useCache('content.datasets');

        $datasets = $this->get('content_datasets', function ($item, $model) use ($item_callback) {

            $item['groups_view'] = self::yamlToArray($item['groups_view']);
            $item['groups_hide'] = self::yamlToArray($item['groups_hide']);
            $item['cats_view']   = self::yamlToArray($item['cats_view']);
            $item['cats_hide']   = self::yamlToArray($item['cats_hide']);
            $item['filters']     = $item['filters'] ? self::yamlToArray($item['filters']) : [];
            $item['sorting']     = $item['sorting'] ? self::yamlToArray($item['sorting']) : [];
            $item['list']        = self::stringToArray($item['list']);

            if (is_callable($item_callback)) {
                $item = call_user_func_array($item_callback, array($item, $model));
                if ($item === false) {
                    return false;
                }
            }

            return $item;
        }, 'name');

        if ($only_visible && $datasets) {
            $user = cmsUser::getInstance();
            foreach ($datasets as $id => $dataset) {
                $is_user_view = $user->isInGroups($dataset['groups_view']);
                $is_user_hide = !empty($dataset['groups_hide']) && $user->isInGroups($dataset['groups_hide']) && !$user->is_admin;
                if (!$is_user_view || $is_user_hide) {
                    unset($datasets[$id]);
                }
            }
        }

        return $datasets;
    }

    public function getContentDataset($id) {

        return cmsEventsManager::hook('ctype_dataset_get', $this->getItemById('content_datasets', $id, function ($item, $model) {

            $item['groups_view'] = self::yamlToArray($item['groups_view']);
            $item['groups_hide'] = self::yamlToArray($item['groups_hide']);
            $item['cats_view']   = self::yamlToArray($item['cats_view']);
            $item['cats_hide']   = self::yamlToArray($item['cats_hide']);
            $item['filters']     = $item['filters'] ? self::yamlToArray($item['filters']) : [];
            $item['sorting']     = $item['sorting'] ? self::yamlToArray($item['sorting']) : [];
            $item['list']        = self::stringToArray($item['list']);

            return $item;
        }));
    }

//============================================================================//
//=============================   КОНТЕНТ   ==================================//
//============================================================================//

    public function resetFilters(){
        parent::resetFilters();
        $this->pub_filtered = false;
        return $this;
    }

    public function enablePubFilter(){
        $this->pub_filter_disabled = false;
        return $this;
    }

    public function disablePubFilter(){
        $this->pub_filter_disabled = true;
        return $this;
    }

    public function filterPublishedOnly(){

        if ($this->pub_filtered) { return $this; }

        $this->pub_filtered = true;

        return $this->filterGtEqual('is_pub', 1);

    }

    public function isFiltersTableExists($ctype_name) {

        return $this->db->isTableExists($this->getContentTypeTableName($ctype_name, '_filters'));
    }

    public function filterPropValue($ctype_name, $prop, $value){

        $table_name  = $this->getContentTypeTableName($ctype_name, '_props_values');
        $table_alias = 'p'.$prop['id'];

        if($prop['handler']->setName($table_alias.'.value')->applyFilter($this, $value) !== false){

            $this->joinInner($table_name, $table_alias, "{$table_alias}.item_id = i.id")->setStraightJoin();

            return $this->filterEqual($table_alias.'.prop_id', $prop['id']);

        }

        return false;
    }

//============================================================================//
//============================================================================//

    public function addContentItem($ctype, $item, $fields){

        $table_name = $this->getContentTypeTableName($ctype['name']);

        $item['user_id'] = empty($item['user_id']) ? cmsUser::getInstance()->id : $item['user_id'];

        if (!empty($item['props'])) {
            $props_values = $item['props'];
            unset($item['props']);
        }

        if (!isset($item['category_id'])) {
            $item['category_id'] = 0;
        }

        if (!empty($item['is_approved'])) {
            $item['date_approved'] = null; // будет CURRENT_TIMESTAMP
        }

        if (!empty($item['new_category'])) {
            $category = $this->addCategory($ctype['name'], [
                'title'     => $item['new_category'],
                'parent_id' => $item['category_id']
            ], !empty($ctype['options']['is_cats_first_level_slug']));

            $item['category_id'] = $category['id'];
        }

        unset($item['new_category']);

        if (!empty($item['new_folder'])) {
            $folder_exists = $this->getContentFolderByTitle($item['new_folder'], $ctype['id'], $item['user_id']);
            if (!$folder_exists) {
                $item['folder_id'] = $this->addContentFolder($ctype['id'], $item['user_id'], $item['new_folder']);
            } else {
                $item['folder_id'] = $folder_exists['id'];
            }
        }

        unset($item['new_folder']);

        $add_cats = [];

        if (isset($item['add_cats'])) {
            foreach ($item['add_cats'] as $cat_id) {
                if (!$cat_id) {
                    continue;
                }
                $add_cats[] = $cat_id;
            }
            unset($item['add_cats']);
        }

        $item['id'] = $this->insert($table_name, $item);

        $this->updateContentItemCategories($ctype['name'], $item['id'], $item['category_id'], $add_cats);

        if (isset($props_values)) {
            $this->addPropsValues($ctype['name'], $item['id'], $props_values);
        }

        if (empty($item['slug'])) {
            $item = array_merge($item, $this->getContentItem($ctype['name'], $item['id']));
            $item['slug'] = $this->getItemSlug($ctype, $item, $fields);
        }

        $this->update($table_name, $item['id'], [
            'slug'               => $item['slug'],
            'date_last_modified' => null
        ]);

        cmsCache::getInstance()->clean('content.list.' . $ctype['name']);
        cmsCache::getInstance()->clean('content.item.' . $ctype['name']);

        $this->fieldsAfterStore($item, $fields, 'add');

        return $item;
    }

    public function updateContentItem($ctype, $id, $item, $fields) {

        $table_name = $this->getContentTypeTableName($ctype['name']);

        if (array_key_exists('date_pub_end', $item)) {
            if ($item['date_pub_end'] === null) {
                $item['date_pub_end'] = false;
            }
        }

        if (!$ctype['is_fixed_url']) {

            if ($ctype['is_auto_url']) {
                $item['slug'] = $this->getItemSlug($ctype, $item, $fields);
            } elseif (!empty($item['slug'])) {
                $item['slug'] = lang_slug($item['slug']);
            }

            if (!empty($item['slug'])) {
                $this->update($table_name, $id, ['slug' => $item['slug']]);
            }
        }

        if (!empty($item['new_category'])) {

            $category = $this->addCategory($ctype['name'], [
                'title'     => $item['new_category'],
                'parent_id' => $item['category_id']
            ], !empty($ctype['options']['is_cats_first_level_slug']));

            $item['category_id'] = $category['id'];
        }

        unset($item['new_category']);

        if (!empty($item['new_folder'])) {
            $folder_exists = $this->getContentFolderByTitle($item['new_folder'], $ctype['id'], $item['user_id']);
            if (!$folder_exists) {
                $item['folder_id'] = $this->addContentFolder($ctype['id'], $item['user_id'], $item['new_folder']);
            } else {
                $item['folder_id'] = $folder_exists['id'];
            }
        }

        unset($item['new_folder']);
        unset($item['folder_title']);

        // удаляем поле SLUG из перечня полей для апдейта,
        // посколько оно могло быть изменено ранее
        $update_item = $item;
        unset($update_item['slug']);

        if (!empty($update_item['props'])) {
            $this->updatePropsValues($ctype['name'], $id, $update_item['props']);
        }

        unset($update_item['props']);
        unset($update_item['user']);
        unset($update_item['user_nickname']);

        $add_cats = [];

        if (isset($update_item['add_cats'])) {
            foreach ($update_item['add_cats'] as $cat_id) {
                if (!$cat_id) {
                    continue;
                }
                $add_cats[] = $cat_id;
            }
            unset($update_item['add_cats']);
        }

        $update_item['date_last_modified'] = null;

        $this->update($table_name, $id, $update_item);

        $this->updateContentItemCategories($ctype['name'], $id, $item['category_id'], $add_cats);

        cmsCache::getInstance()->clean('content.list.' . $ctype['name']);
        cmsCache::getInstance()->clean('content.item.' . $ctype['name']);

        $this->fieldsAfterStore($item, $fields, 'edit');

        return $item;
    }

    public function updateContentItemTags($ctype_name, $id, $tags) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $this->update($table_name, $id, [
            'tags' => $tags
        ]);

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

    }

    public function replaceCachedTags($ctype_name, $ids, $new_tag, $old_tag) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $old_tag = $this->db->escape($old_tag);
        $new_tag = $this->db->escape($new_tag);

        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $k => $v) {
            $v = $this->db->escape($v);
            $ids[$k] = "'{$v}'";
        }
        $ids = implode(',', $ids);

        $this->db->query("UPDATE `{#}{$table_name}` SET `tags` = REPLACE(`tags`, '{$old_tag}', '$new_tag') WHERE id IN ({$ids})");

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

    }

//============================================================================//
//============================================================================//

    public function getItemSlug($ctype, $item, $fields, $check_slug = true) {

        $slug_len = 100;

        $pattern = trim($ctype['url_pattern'], '/');

        preg_match_all('/{([a-z0-9\_]+)}/i', $pattern, $matches);

        if (!$matches) {
            return lang_slug($item['id'], false);
        }

        $item['ctype_name'] = $ctype['name'];
        $item['ctype'] = $ctype;

        list($tags, $names) = $matches;

        if (in_array('category', $names)) {
            $category = $this->getCategory($ctype['name'], $item['category_id']);
            $pattern  = str_replace('{category}', $category['slug'], $pattern);
            unset($names[array_search('category', $names)]);
        }

        $pattern = trim($pattern, '/');

        foreach ($names as $idx => $field_name) {

            $value = get_localized_value($field_name, $item);

            if ($value) {

                $value = str_replace('/', '', $value);

                if (isset($fields[$field_name])) {

                    $value = $fields[$field_name]['handler']->setItem($item)->getStringValue($value);

                    $value = lang_slug(trim($value, '/'), false);
                }

                $pattern = str_replace($tags[$idx], $value, $pattern);
            }
        }

        $slug = $pattern;

        $slug = mb_substr($slug, 0, $slug_len);

        if (!$check_slug) {
            return $slug;
        }

        $slug = $this->checkCorrectEqualSlug($this->getContentTypeTableName($ctype['name']), $slug, $item['id'], $slug_len);

        return $slug;
    }

//============================================================================//
//============================================================================//

    public function getContentItemCategories($ctype_name, $id) {

        return $this->filterEqual('item_id', $id)->
                get($this->getContentTypeTableName($ctype_name, '_cats_bind'), function ($item, $model) {
            return $item['category_id'];
        }, false);
    }

    public function getContentItemCategoriesList($ctype_name, $id) {

        $bind_table_name = $this->getContentTypeTableName($ctype_name, '_cats_bind');
        $cats_table_name = $this->getContentCategoryTableName($ctype_name);

        $this->join($bind_table_name, 'b', 'b.category_id = i.id');

        $this->filterEqual('b.item_id', $id);

        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($cats_table_name);
    }

    public function moveContentItemsToCategory($ctype, $from_id, $to_id, $items_ids, $fields) {

        $table_name = $this->getContentTypeTableName($ctype['name']);
        $binds_table_name = $this->getContentTypeTableName($ctype['name'], '_cats_bind');

        $items = $this->filterIn('id', $items_ids)->get($table_name);

        foreach($items as $item){

            $this->
                    filterEqual('item_id', $item['id'])->
                    filterEqual('category_id', $from_id)->
                    deleteFiltered($binds_table_name);

            $this->
                filterEqual('item_id', $item['id'])->
                filterEqual('category_id', $item['category_id'])->
                deleteFiltered($binds_table_name);

            $is_bind_exists = $this->
                                filterEqual('item_id', $item['id'])->
                    filterEqual('category_id', $to_id)->
                                getCount($binds_table_name, 'item_id');

            $this->resetFilters();

            if (!$is_bind_exists){

                $this->insert($binds_table_name, [
                    'item_id' => $item['id'],
                    'category_id' => $to_id
                ]);
            }

            $item['category_id'] = $to_id;

            if (!$ctype['is_fixed_url'] && $ctype['is_auto_url']){

                $item['slug'] = $this->getItemSlug($ctype, $item, $fields);

                $this->update($table_name, $item['id'], ['slug' => $item['slug']]);
            }
        }

        $this->filterIn('id', $items_ids)->updateFiltered($table_name, [
            'category_id' => $to_id
        ]);

        cmsCache::getInstance()->clean('content.list.'.$ctype['name']);
        cmsCache::getInstance()->clean('content.item.'.$ctype['name']);

        return true;
    }

    public function updateContentItemCategories($ctype_name, $id, $category_id, $add_cats) {

        $table_name = $this->getContentTypeTableName($ctype_name, '_cats_bind');

        $new_cats = empty($add_cats) ? [] : $add_cats;

        if (!$category_id) {
            $category_id = 1;
        }

        if (!in_array($category_id, $new_cats)) {
            $new_cats[] = $category_id;
        }

        $current_cats = $this->
                filterEqual('item_id', $id)->
                get($table_name, function ($item, $model) {
            return $item['category_id'];
        }, false);

        if ($current_cats) {
            foreach ($current_cats as $current_cat_id) {
                if (!in_array($current_cat_id, $new_cats)) {
                    $this->
                            filterEqual('item_id', $id)->
                            filterEqual('category_id', $current_cat_id)->
                            deleteFiltered($table_name);
                }
            }
        }

        foreach ($new_cats as $new_cat_id) {
            if (!$current_cats || !in_array($new_cat_id, $current_cats)) {
                $this->insert($table_name, [
                    'item_id'     => $id,
                    'category_id' => $new_cat_id
                ]);
            }
        }

    }

//============================================================================//
//============================================================================//

    public function restoreContentItem($ctype_name, $id) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        if (is_numeric($id)) {
            $item = $this->getContentItem($ctype_name, $id);
            if (!$item) { return false; }
        } else {
            $item = $id;
        }

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        $success = $this->update($table_name, $item['id'], ['is_deleted' => null]);

        cmsEventsManager::hook('content_after_restore', [$ctype_name, $item]);
        cmsEventsManager::hook("content_{$ctype_name}_after_restore", $item);

        return $success;
    }

    public function toTrashContentItem($ctype_name, $id) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        if (is_numeric($id)) {
            $item = $this->getContentItem($ctype_name, $id);
            if (!$item) { return false; }
        } else {
            $item = $id;
        }

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        $success = $this->update($table_name, $item['id'], ['is_deleted' => 1]);

        cmsEventsManager::hook('content_after_trash_put', [$ctype_name, $item]);
        cmsEventsManager::hook("content_{$ctype_name}_after_trash_put", $item);

        return $success;
    }

    public function deleteContentItem($ctype_name, $id) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $item = $this->getContentItem($ctype_name, $id);
        if (!$item) { return false; }

        $ctype = $this->getContentTypeByName($ctype_name);

        $item['ctype'] = $ctype;
        $item['ctype_name'] = $ctype['name'];

        cmsEventsManager::hook('content_before_delete', ['ctype_name' => $ctype_name, 'item' => $item]);
        cmsEventsManager::hook("content_{$ctype_name}_before_delete", $item);

        $fields = $this->getContentFields($ctype_name, $id);

        foreach ($fields as $field) {
            $field['handler']->setItem($item)->delete(isset($item[$field['name']]) ? $item[$field['name']] : null);
        }

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        $this->deletePropsValues($ctype_name, $id);

        $this->deleteContentItemRelations($ctype['id'], $id);

        $this->filterEqual('item_id', $item['id'])->deleteFiltered($table_name . '_cats_bind');

        $success = $this->delete($table_name, $id);

        if ($success) {
            cmsEventsManager::hook('content_after_delete', ['ctype_name' => $ctype_name, 'ctype' => $ctype, 'item' => $item]);
            cmsEventsManager::hook("content_{$ctype_name}_after_delete", $item);
        }

        return $success;
    }

    public function deleteUserContent($user_id){

        $ctypes = $this->getContentTypes();

        foreach($ctypes as $ctype){

            $this->disableDeleteFilter()->disableApprovedFilter()->
                    disablePubFilter()->disablePrivacyFilter();

            $items = $this->filterEqual('user_id', $user_id)->getContentItems($ctype['name']);

            if (is_array($items)){
                foreach($items as $item){
                    $this->deleteContentItem($ctype['name'], $item['id']);
                }
            }

        }

        $this->filterEqual('user_id', $user_id)->deleteFiltered('content_folders');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('moderators');

    }

//============================================================================//
//============================================================================//

    public function applyPrivacyFilter($ctype, $allow_view_all = false) {

        $hide_except_title = (!empty($ctype['options']['privacy_type']) && $ctype['options']['privacy_type'] === 'show_title');

        // Сначала проверяем настройки типа контента
        if (!empty($ctype['options']['privacy_type']) &&
                in_array($ctype['options']['privacy_type'], array('show_title', 'show_all'), true)) {

            $this->disablePrivacyFilter();
            if ($ctype['options']['privacy_type'] != 'show_title') {
                $hide_except_title = false;
            }
        }

        // А потом, если разрешено правами доступа, отключаем фильтр приватности
        if ($allow_view_all) {
            $this->disablePrivacyFilter();
            $hide_except_title = false;
        }

        return $hide_except_title;
    }

    public function getContentItemsCount($ctype_name) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }
        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }
        if (!$this->pub_filter_disabled) { $this->filterPublishedOnly(); }
        if (!$this->hidden_parents_filter_disabled) { $this->filterHiddenParents(); }

        $this->useCache("content.list.{$ctype_name}");

        return $this->getCount($table_name);
    }

    public function getContentItemsForSitemap($ctype_name, $fields = []) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $this->selectOnly('slug');
        $this->select('date_last_modified');
        $this->select('title');

        if ($fields) {
            foreach ($fields as $field) {
                $this->select($field);
            }
        }

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }
        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }
        if (!$this->pub_filter_disabled) { $this->filterPublishedOnly(); }
        if (!$this->hidden_parents_filter_disabled) { $this->filterHiddenParents(); }

        if (!$this->order_by) {
            $this->orderBy('date_pub', 'desc')->forceIndex('date_pub');
        }

        return $this->get($table_name, false, false);
    }

    public function selectFieldsForList($ctype_name, $fields) {

        $ctype_table = $this->getContentTypeTableName($ctype_name);

        $table_fields = $this->db->getTableFields($ctype_table);

        $excluded_fields = ['seo_keys', 'seo_desc', 'seo_title'];

        foreach($fields as $field){
            if (!$field['is_in_list']) {
                $excluded_fields[] = $field['name'];
            }
        }

        $select_fields = array_diff($table_fields, $excluded_fields);

        return $this->selectList($select_fields, true, $ctype_table);
    }

    public function getContentItems($ctype_name, $callback = null) {

        $cat_table = $this->getContentCategoryTableName($ctype_name);

        $this->joinUser();

        $this->select('f.title', 'folder_title');
        $this->joinLeft('content_folders', 'f', 'f.id = i.folder_id');

        $this->selectTranslatedField('cat.title', $cat_table, 'cat_title');
        $this->select('cat.slug', 'cat_slug');
        $this->select('cat.id', 'category_id');
        $this->joinLeft($cat_table, 'cat', 'cat.id = i.category_id');

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }
        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }
        if (!$this->pub_filter_disabled) { $this->filterPublishedOnly(); }

        if (!$this->order_by) {
            $this->orderBy('date_pub', 'desc')->forceIndex('date_pub');
        }

        $this->useCache('content.list.' . $ctype_name);

        $user = cmsUser::getInstance();

        return $this->get($this->getContentTypeTableName($ctype_name), function ($item, $model) use ($user, $callback, $ctype_name) {

            $item['category'] = [
                'id'    => $item['category_id'],
                'slug'  => $item['cat_slug'],
                'title' => $item['cat_title']
            ];

            $item['user'] = [
                'id'              => $item['user_id'],
                'slug'            => $item['user_slug'],
                'nickname'        => $item['user_nickname'],
                'avatar'          => $item['user_avatar'],
                'groups'          => $item['user_groups'],
                'privacy_options' => self::yamlToArray($item['user_privacy_options']),
                'is_friend'       => $user->isFriend($item['user_id'])
            ];

            if (is_callable($callback)) {
                $item = $callback($item, $model, $ctype_name, $user);
            }

            return $item;
        });
    }

//============================================================================//
//============================================================================//

    public function getContentItem($ctype_name, $id, $by_field = 'id') {

        if (is_numeric($ctype_name)) {
            $ctype = $this->getContentType($ctype_name);
        } else {
            $ctype = $this->getContentTypeByName($ctype_name);
        }

        if (!$ctype) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name']);

        $this->select('f.title', 'folder_title');

        $this->joinUser();
        $this->joinLeft('content_folders', 'f', 'f.id = i.folder_id');

        $this->useCache("content.item.{$ctype['name']}");

        return $this->getItemByField($table_name, $by_field, $id, function ($item, $model) use ($ctype) {

            $item['user'] = [
                'id'              => $item['user_id'],
                'groups'          => $item['user_groups'],
                'slug'            => $item['user_slug'],
                'nickname'        => $item['user_nickname'],
                'privacy_options' => self::yamlToArray($item['user_privacy_options']),
                'avatar'          => $item['user_avatar']
            ];

            $item['is_draft'] = false;

            if (!$item['is_approved']) {
                $item['is_draft'] = $model->isDraftContentItem($ctype['name'], $item);
            }

            return $item;
        }, $by_field);
    }

    public function getContentItemBySLUG($ctype_name, $slug) {
        return $this->getContentItem($ctype_name, $slug, 'slug');
    }

//============================================================================//
//============================================================================//

    public function getUserContentItemsCount($ctype_name, $user_id, $is_only_approved = true){

        $this->filterEqual('user_id', $user_id);

        if (!$is_only_approved) { $this->approved_filter_disabled = true; }

        $count = $this->getContentItemsCount( $ctype_name );

        $this->resetFilters();

        return $count;

    }

    public function getUserContentItemsCount24($ctype_name, $user_id){

        $this->filter("DATE(DATE_FORMAT(i.date_pub, '%Y-%m-%d')) = CURDATE()");

        return $this->getUserContentItemsCount($ctype_name, $user_id, false);

    }

    public function getUserContentCounts($user_id, $is_filter_hidden=false, $access_callback = false){

        $counts = array();

        $ctypes = $this->getContentTypes();

        $this->filterEqual('user_id', $user_id);

        if ($is_filter_hidden){
            $this->enableHiddenParentsFilter();
        }

        if (!$is_filter_hidden){
            $this->disableApprovedFilter();
            $this->disablePubFilter();
            $this->disablePrivacyFilter();
        }

        foreach($ctypes as $ctype){

            if(is_callable($access_callback) && !$access_callback($ctype)){
                continue;
            }

            if(!$ctype['options']['profile_on']){
                continue;
            }

            $count = $this->getContentItemsCount( $ctype['name'] );

            if ($count) {

                $counts[ $ctype['name'] ] = array(
                    'count' => $count,
                    'is_in_list' => $ctype['options']['profile_on'],
                    'title' => empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']
                );

            }

        }

        $this->resetFilters();

        return $counts;

    }

//============================================================================//
//============================================================================//

    public function publishDelayedContentItems($ctype_name, $pub_item_ids) {

        return $this->filterIn('id', $pub_item_ids)->
                updateFiltered($this->getContentTypeTableName($ctype_name), [
                    'is_pub' => 1
                ]);
    }

    public function hideExpiredContentItems($ctype_name) {

        return $this->filterIsNull('is_deleted')->
                filterGtEqual('is_pub', 1)->
                filterNotNull('date_pub_end')->
                filter('i.date_pub_end <= NOW()')->
                updateFiltered($this->getContentTypeTableName($ctype_name), [
                    'is_pub' => 0
                ]);
    }

    public function deleteExpiredContentItems($ctype_name) {

        return $this->selectOnly('id')->
                filterIsNull('is_deleted')->
                filterGtEqual('is_pub', 1)->
                filterNotNull('date_pub_end')->
                filter('i.date_pub_end <= NOW()')->
                get($this->getContentTypeTableName($ctype_name), function ($item, $model) use ($ctype_name) {
                    $model->deleteContentItem($ctype_name, $item['id']);
                    return $item['id'];
                });
    }

    public function toTrashExpiredContentItems($ctype_name) {

        return $this->selectOnly('id')->
                filterIsNull('is_deleted')->
                filterGtEqual('is_pub', 1)->
                filterNotNull('date_pub_end')->
                filter('i.date_pub_end <= NOW()')->
                get($this->getContentTypeTableName($ctype_name), function ($item, $model) use ($ctype_name) {
                    $model->toTrashContentItem($ctype_name, $item);
                    return $item['id'];
                });
    }

    public function toggleContentItemPublication($ctype_name, $id, $is_pub) {

        $this->update($this->getContentTypeTableName($ctype_name), $id, [
            'is_pub' => $is_pub
        ]);

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        return true;
    }

    public function incrementHitsCounter($ctype_name, $id) {

        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        return $this->filterEqual('id', $id)->increment($this->getContentTypeTableName($ctype_name), 'hits_count');
    }

//============================================================================//
//============================================================================//

    public function deleteCategory($ctype_name, $id, $is_delete_content = false) {

        $category = $this->getCategory($ctype_name, $id);

        $this->filterCategory($ctype_name, $category, true, true);

        if (!$is_delete_content) {

            $this->updateFiltered($this->getContentTypeTableName($ctype_name), [
                'category_id' => 1
            ]);

        } else {

            $this->disableDeleteFilter()->disableApprovedFilter()->
                    disablePubFilter()->disablePrivacyFilter();

            $items = $this->getContentItems($ctype_name);

            if ($items) {
                foreach ($items as $item) {
                    $this->deleteContentItem($ctype_name, $item['id']);
                }
            }
        }

        $this->unbindContentProps($ctype_name, $id);

        return parent::deleteCategory($ctype_name, $id);
    }

//============================================================================//
//============================================================================//

    public function getRatingTarget($ctype_name, $id) {

        $ctype = $this->getContentTypeByName($ctype_name);

        if (!$ctype) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name']);

        $item = $this->getItemById($table_name, $id);

        if ($item) {
            $item['page_url'] = href_to($ctype_name, $item['slug'] . '.html');
        }

        return $item;
    }

    public function updateRating($ctype_name, $id, $rating) {

        $ctype = $this->getContentTypeByName($ctype_name);

        if (!$ctype) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name']);

        cmsCache::getInstance()->clean('content.list.' . $ctype['name']);
        cmsCache::getInstance()->clean('content.item.' . $ctype['name']);

        return $this->update($table_name, $id, ['rating' => $rating]);
    }

//============================================================================//
//============================================================================//

    public function updateCommentsCount($ctype_name, $id, $comments_count) {

        $ctype = $this->getContentTypeByName($ctype_name);

        if (!$ctype) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name']);

        cmsCache::getInstance()->clean('content.list.' . $ctype['name']);
        cmsCache::getInstance()->clean('content.item.' . $ctype['name']);

        return $this->update($table_name, $id, ['comments' => $comments_count]);
    }

    public function getCommentsOptions($ctype_name) {

        $ctype = $this->getContentTypeByName($ctype_name);

        if (!$ctype) {
            return [];
        }

        return [
            'enable'        => $ctype['is_comments'],
            'title_pattern' => (!empty($ctype['options']['comments_title_pattern']) ? $ctype['options']['comments_title_pattern'] : ''),
            'labels'        => (!empty($ctype['options']['comments_labels']) ? $ctype['options']['comments_labels'] : []),
            'template'      => (!empty($ctype['options']['comments_template']) ? $ctype['options']['comments_template'] : '')
        ];
    }

    public function getTargetItemInfo($ctype_name, $id) {

        $ctype = $this->getContentTypeByName($ctype_name);

        if (!$ctype) {
            return false;
        }

        $item = $this->getContentItem($ctype['name'], $id);

        if (!$item) {
            return false;
        }

        return [
            'url'        => href_to_rel($ctype_name, $item['slug'] . '.html'),
            'title'      => $item['title'],
            'is_private' => $item['is_private'] || $item['is_parent_hidden']
        ];
    }

//============================================================================//
//============================================================================//

    public function toggleParentVisibility($parent_type, $parent_id, $is_hidden) {

        $ctypes_names = $this->getContentTypesNames();

        $is_hidden = $is_hidden ? 1 : null;

        foreach ($ctypes_names as $ctype_name) {

            $table_name = $this->getContentTypeTableName($ctype_name);

            $this->
                    filterEqual('parent_type', $parent_type)->
                    filterEqual('parent_id', $parent_id)->
                    updateFiltered($table_name, ['is_parent_hidden' => $is_hidden]);

            cmsCache::getInstance()->clean('content.list.' . $ctype_name);
            cmsCache::getInstance()->clean('content.item.' . $ctype_name);
        }

    }

    public function isDraftContentItem($ctype_name, $item) {

        if(!empty($item['is_approved'])){ return false; }

        return !(bool)$this->selectOnly('id')->filterEqual('ctype_name', $ctype_name)->
                    filterEqual('item_id', $item['id'])->getItem('moderators_tasks');
    }

    public function getDraftCounts($user_id) {

        $counts = [];

        $ctypes = $this->getContentTypes();

        foreach ($ctypes as $ctype) {

            $this->useCache("content.list.{$ctype['name']}");

            $this->filterEqual('user_id', $user_id);
            $this->filterEqual('is_approved', 0);
            $this->disableApprovedFilter();
            $this->disablePubFilter();
            $this->disablePrivacyFilter();

            $this->joinExcludingLeft('moderators_tasks', 't', 't.item_id', 'i.id', "t.ctype_name = '{$ctype['name']}'");

            $count = $this->getContentItemsCount($ctype['name']);

            $this->resetFilters();

            if ($count) {
                $counts[$ctype['name']] = $count;
            }
        }

        return $counts;
    }

    public function approveContentItem($ctype_name, $id, $moderator_user_id) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $this->update($table_name, $id, [
            'is_approved'   => 1,
            'approved_by'   => $moderator_user_id,
            'date_approved' => ''
        ]);

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        return true;
    }

    public function unbindParent($ctype_name, $id) {

        $table_name = $this->getContentTypeTableName($ctype_name);

        $this->update($table_name, $id, [
            'parent_id'        => null,
            'parent_type'      => null,
            'parent_title'     => null,
            'parent_url'       => null,
            'is_parent_hidden' => null
        ]);

        cmsCache::getInstance()->clean('content.list.' . $ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $ctype_name);

        return true;
    }

    /**
     * @deprecated
     *
     * Метод для совместимости
     */
    public function userIsContentTypeModerator($ctype_name, $user_id){
        return cmsCore::getModel('moderation')->userIsContentModerator($ctype_name, $user_id);
    }
}
