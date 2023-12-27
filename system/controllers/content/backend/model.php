<?php
/**
 * Класс модели, предназначенный для бэкенда
 */
class modelBackendContent extends modelContent {

    public function loadAllCtypes() {
        return $this->reloadAllCtypes(false);
    }

    public function getContentType($id, $by_field = 'id') {
        return $this->getItemByField('content_types', $by_field, $id, [$this, 'contentTypesCallback']);
    }

    public function getContentTypeByName($name) {
        return $this->getContentType($name, 'name');
    }

    public function addContentType($ctype) {

        if (!isset($ctype['labels'])) {
            $ctype['labels'] = [
                'one'     => $ctype['name'],
                'two'     => $ctype['name'],
                'many'    => $ctype['name'],
                'create'  => $ctype['name'],
                'list'    => '',
                'profile' => ''
            ];
        }

        $id = $this->insert('content_types', $ctype);

        $config = cmsConfig::getInstance();

        // получаем структуру таблиц для хранения контента данного типа
        $content_table_struct      = $this->getContentTableStruct();
        $fields_table_struct       = $this->getFieldsTableStruct();
        $props_table_struct        = $this->getPropsTableStruct();
        $props_bind_table_struct   = $this->getPropsBindTableStruct();
        $props_values_table_struct = $this->getPropsValuesTableStruct();

        // создаем таблицы
        $table_name = $this->table_prefix . $ctype['name'];

        $this->db->createTable($table_name, $content_table_struct, ($config->innodb_full_text ? $config->db_engine : 'MYISAM'));
        $this->db->createTable("{$table_name}_fields", $fields_table_struct, $config->db_engine);
        $this->db->createCategoriesTable("{$table_name}_cats");
        $this->db->createCategoriesBindsTable("{$table_name}_cats_bind");

        $this->db->createTable("{$table_name}_props", $props_table_struct, $config->db_engine);
        $this->db->createTable("{$table_name}_props_bind", $props_bind_table_struct, $config->db_engine);
        $this->db->createTable("{$table_name}_props_values", $props_values_table_struct, $config->db_engine);

        //
        // добавляем стандартные поля
        //
        // заголовок
        $this->addContentField($ctype['name'], [
            'name'          => 'title',
            'title'         => LANG_TITLE,
            'type'          => 'caption',
            'ctype_id'      => $id,
            'is_in_list'    => 1,
            'is_in_item'    => 1,
            'is_in_filter'  => 1,
            'is_fixed'      => 1,
            'is_fixed_type' => 1,
            'is_system'     => 0,
            'options'       => [
                'label_in_list'      => 'none',
                'label_in_item'      => 'none',
                'min_length'         => 3,
                'max_length'         => 255,
                'is_required'        => true,
                'in_fulltext_search' => true
            ]
        ], true);

        // дата публикации
        $this->addContentField($ctype['name'], [
            'name'          => 'date_pub',
            'title'         => LANG_DATE_PUB,
            'type'          => 'date',
            'ctype_id'      => $id,
            'is_in_list'    => 1,
            'is_in_item'    => 1,
            'is_in_filter'  => 1,
            'is_fixed'      => 1,
            'is_fixed_type' => 1,
            'is_system'     => 1,
            'options'       => [
                'label_in_list' => 'none',
                'label_in_item' => 'left',
                'show_time'     => true
            ]
        ], true);

        // автор
        $this->addContentField($ctype['name'], [
            'name'          => 'user',
            'title'         => LANG_AUTHOR,
            'type'          => 'user',
            'ctype_id'      => $id,
            'is_in_list'    => 1,
            'is_in_item'    => 1,
            'is_in_filter'  => 0,
            'is_fixed'      => 1,
            'is_fixed_type' => 1,
            'is_system'     => 1,
            'options'       => [
                'label_in_list' => 'none',
                'label_in_item' => 'left'
            ]
        ], true);

        // фотография
        $this->addContentField($ctype['name'], [
            'name'       => 'photo',
            'title'      => LANG_PHOTO,
            'type'       => 'image',
            'ctype_id'   => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_fixed'   => 1,
            'options'    => [
                'size_teaser' => 'small',
                'size_full'   => 'normal',
                'sizes'       => ['micro', 'small', 'normal', 'big']
            ]
        ], true);

        // описание
        $this->addContentField($ctype['name'], [
            'name'       => 'content',
            'title'      => LANG_DESCRIPTION,
            'type'       => 'text',
            'ctype_id'   => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_fixed'   => 1,
            'options'    => [
                'is_strip_tags'  => 1,
                'is_html_filter' => 1,
                'label_in_list'  => 'none',
                'label_in_item'  => 'none'
            ]
        ], true);

        cmsCache::getInstance()->clean('content.types');

        return $id;
    }

    public function updateContentType($id, $item){

        cmsCache::getInstance()->clean('content.types');

        return $this->update('content_types', $id, $item);
    }

    public function deleteContentType($id){

        $ctype = $this->getContentType($id);

        if ($ctype['is_fixed']) { return false; }

        $this->disableDeleteFilter()->disableApprovedFilter()->
                disablePubFilter()->disablePrivacyFilter();

		$items = $this->getContentItems($ctype['name']);
		if ($items){
			foreach($items as $item){
				$this->deleteContentItem($ctype['name'], $item['id']);
			}
		}

		cmsCore::getModel('tags')->recountTagsFrequency();

        $this->delete('content_types', $id);
        $this->delete('content_datasets', $id, 'ctype_id');

        $table_name = $this->table_prefix . $ctype['name'];

        $this->db->dropTable("{$table_name}");
        $this->db->dropTable("{$table_name}_fields");
        $this->db->dropTable("{$table_name}_cats");
        $this->db->dropTable("{$table_name}_filters");
        $this->db->dropTable("{$table_name}_cats_bind");
        $this->db->dropTable("{$table_name}_props");
        $this->db->dropTable("{$table_name}_props_bind");
        $this->db->dropTable("{$table_name}_props_values");

        cmsCache::getInstance()->clean('content.types');

        // связь как родитель
        $relations = $this->getContentRelations($id);

        if($relations){
            foreach ($relations as $relation) {

                $this->deleteContentRelation($relation['id']);

                $parent_field_name = "parent_{$ctype['name']}_id";

                if($relation['target_controller'] != 'content'){

                    $this->setTablePrefix('');

                    $target_ctype = array(
                        'name' => $relation['target_controller']
                    );

                } else {

                    $this->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

                    $target_ctype = $this->getContentType($relation['child_ctype_id']);

                }

                if ($this->isContentFieldExists($target_ctype['name'], $parent_field_name)){
                    $this->deleteContentField($target_ctype['name'], $parent_field_name, 'name', true);
                }

            }
        }

        // связь как дочка
        $relations = $this->filterEqual('child_ctype_id', $id)->getContentRelations();
        if($relations){
            foreach ($relations as $relation) {
                $this->deleteContentRelation($relation['id']);
            }
        }

        return true;
    }

    public function reorderContentTypes($ctypes_ids_list){

        $this->reorderByList('content_types', $ctypes_ids_list);

        cmsCache::getInstance()->clean('content.types');

        return true;
    }

//============================================================================//
//=======================   Структуры таблиц   ===============================//
//============================================================================//

    public function getContentTableStruct() {

        return [
            'id'                 => ['type' => 'primary'],
            'title'              => ['type' => 'varchar', 'size' => 255, 'fulltext' => true],
            'content'            => ['type' => 'text'],
            'photo'              => ['type' => 'text'],
            'slug'               => ['type' => 'varchar', 'index' => true, 'size' => 100],
            'seo_keys'           => ['type' => 'varchar', 'size' => 256],
            'seo_desc'           => ['type' => 'varchar', 'size' => 256],
            'seo_title'          => ['type' => 'varchar', 'size' => 256],
            'tags'               => ['type' => 'varchar', 'size' => 1000],
            'template'           => ['type' => 'varchar', 'size' => 150],
            'date_pub'           => ['type' => 'timestamp', 'index' => ['date_pub', 'parent_id', 'user_id'], 'composite_index' => [4, 3, 2, 1], 'default_current' => true],
            'date_last_modified' => ['type' => 'timestamp'],
            'date_pub_end'       => ['type' => 'timestamp', 'index' => true],
            'is_pub'             => ['type' => 'tinyint', 'size' => 1, 'index' => 'date_pub', 'composite_index' => 0, 'default' => 1],
            'hits_count'         => ['type' => 'int', 'default' => 0, 'unsigned' => true],
            'user_id'            => ['type' => 'int', 'index' => 'user_id', 'composite_index' => 0, 'unsigned' => true],
            'parent_id'          => ['type' => 'int', 'index' => 'parent_id', 'composite_index' => 0, 'unsigned' => true],
            'parent_type'        => ['type' => 'varchar', 'size' => 32, 'index' => 'parent_id', 'composite_index' => 1],
            'parent_title'       => ['type' => 'varchar', 'size' => 100],
            'parent_url'         => ['type' => 'varchar', 'size' => 255],
            'is_parent_hidden'   => ['type' => 'bool', 'index' => 'date_pub', 'composite_index' => 1],
            'category_id'        => ['type' => 'int', 'index' => true, 'default' => 1, 'unsigned' => true],
            'folder_id'          => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'is_comments_on'     => ['type' => 'bool', 'default' => 1],
            'comments'           => ['type' => 'int', 'default' => 0, 'unsigned' => true],
            'rating'             => ['type' => 'int', 'default' => 0],
            'is_deleted'         => ['type' => 'bool', 'index' => 'date_pub', 'composite_index' => 2],
            'is_approved'        => ['type' => 'bool', 'index' => 'date_pub', 'composite_index' => 3, 'default' => 1],
            'approved_by'        => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'date_approved'      => ['type' => 'timestamp'],
            'is_private'         => ['type' => 'bool', 'default' => 0]
        ];
    }

    public function getFieldsTableStruct() {

        return [
            'id'            => ['type' => 'primary'],
            'ctype_id'      => ['type' => 'int', 'unsigned' => true],
            'name'          => ['type' => 'varchar', 'size' => 40],
            'title'         => ['type' => 'varchar', 'size' => 100],
            'hint'          => ['type' => 'varchar', 'size' => 200],
            'ordering'      => ['type' => 'int', 'index' => 'is_enabled', 'composite_index' => 1, 'unsigned' => true],
            'is_enabled'    => ['type' => 'bool', 'index' => 'is_enabled', 'composite_index' => 0, 'default' => 1],
            'fieldset'      => ['type' => 'varchar', 'size' => 32],
            'type'          => ['type' => 'varchar', 'size' => 16],
            'is_in_list'    => ['type' => 'bool'],
            'is_in_item'    => ['type' => 'bool'],
            'is_in_filter'  => ['type' => 'bool'],
            'is_private'    => ['type' => 'bool'],
            'is_fixed'      => ['type' => 'bool'],
            'is_fixed_type' => ['type' => 'bool'],
            'is_system'     => ['type' => 'bool'],
            'values'        => ['type' => 'text'],
            'options'       => ['type' => 'text'],
            'groups_read'   => ['type' => 'text'],
            'groups_add'    => ['type' => 'text'],
            'groups_edit'   => ['type' => 'text'],
            'filter_view'   => ['type' => 'text']
        ];
    }

    public function getPropsTableStruct() {

        return [
            'id'           => ['type' => 'primary'],
            'ctype_id'     => ['type' => 'int', 'unsigned' => true],
            'title'        => ['type' => 'varchar', 'size' => 100],
            'fieldset'     => ['type' => 'varchar', 'size' => 32],
            'type'         => ['type' => 'varchar', 'size' => 16],
            'is_in_filter' => ['type' => 'bool', 'index' => true],
            'values'       => ['type' => 'text'],
            'options'      => ['type' => 'text']
        ];
    }

    public function getPropsBindTableStruct(){

        return [
            'id'       => ['type' => 'primary'],
            'prop_id'  => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'cat_id'   => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'ordering' => ['type' => 'int', 'index' => true, 'unsigned' => true]
        ];
    }

    public function getPropsValuesTableStruct(){

        return [
            'prop_id' => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'item_id' => ['type' => 'int', 'index' => true, 'unsigned' => true],
            'value'   => ['type' => 'varchar', 'size' => 255]
        ];
    }

//============================================================================//
//==============================   НАБОРЫ   ==================================//
//============================================================================//

    public function addContentDatasetIndex($dataset, $ctype_name) {

        $content_table_name = $this->table_prefix.$ctype_name;
        $index_name         = 'dataset_'.$dataset['name'];

        // поля для индекса
        $filters_fields = $sorting_fields = $fields = array();

        // создаем индекс
        // параметры выборки
        if($dataset['filters']){
            foreach ($dataset['filters'] as $filters) {
                if($filters && !in_array($filters['condition'], array('gt','lt','ge','le','nn','ni'))){
                    $filters_fields[] = $filters['field'];
                }
            }
            $filters_fields = array_unique($filters_fields);
        }
        // добавим условия, которые в каждой выборке
        // только для записей типов контента
        if($this->table_prefix){
            $filters_fields[] = 'is_pub';
            $filters_fields[] = 'is_parent_hidden';
            $filters_fields[] = 'is_deleted';
            $filters_fields[] = 'is_approved';
        }
        // сортировка
        if($dataset['sorting']){
            foreach ($dataset['sorting'] as $sorting) {
                if($sorting){
                    $sorting_fields[] = $sorting['by'];
                }
            }
            $sorting_fields = array_unique($sorting_fields);
        }

        // если поле присутствует и в выборке и в сортировке, оставляем только в сортировке
        if($filters_fields){
            foreach ($filters_fields as $key => $field) {
                if(in_array($field, $sorting_fields)){
                    unset($filters_fields[$key]);
                }
            }
        }

        $fields = array_merge($filters_fields, $sorting_fields);

        if(!$fields){ return null; }

        if($fields == array('date_pub')){
            $index_name = 'date_pub';
        } elseif($fields == array('user_id','date_pub') || $fields == array('user_id')){
            $index_name = 'user_id';
        } else {

            // ищем индекс с таким же набором полей
            $is_found = false;
            $indexes = $this->db->getTableIndexes($content_table_name);
            foreach ($indexes as $_index_name => $_index_fields) {
                if($fields == $_index_fields){
                    $is_found = $_index_name; break;
                }
            }

            // нашли - используем его
            if($is_found){
                $index_name = $is_found;
            } else {

                // если нет, то создаем новый
                $this->db->addIndex($content_table_name, $fields, $index_name);

            }

        }

        return $index_name;

    }

    public function addContentDataset($dataset, $ctype){

        $table_name = 'content_datasets';

        $dataset['ctype_id'] = $ctype['id'];

        $this->filterEqual('ctype_id', $dataset['ctype_id']);

        $dataset['ordering'] = $this->getNextOrdering($table_name);

        $dataset['index'] = $this->addContentDatasetIndex($dataset, $ctype['name']);

        $dataset['list'] = cmsModel::arrayToString($dataset['list']);

        $dataset['id'] = $this->insert($table_name, $dataset);

        cmsEventsManager::hook('ctype_dataset_add', array($dataset, $ctype, $this));

        cmsCache::getInstance()->clean('content.datasets');

        return $dataset['id'];

    }

    public function deleteContentDatasetIndex($ctype_name, $index_name) {

        // если используется в других датасетах, не удаляем
        if($this->getItemByField('content_datasets', 'index', $index_name)){
            return false;
        }

        return $this->db->dropIndex($this->table_prefix.$ctype_name, $index_name);

    }

    public function updateContentDataset($id, $dataset, $ctype, $old_dataset){

        $dataset['ctype_id'] = $ctype['id'];

        $dataset['list'] = cmsModel::arrayToString($dataset['list']);

        $success = $this->update('content_datasets', $id, $dataset);

        $dataset['id'] = $id;
        cmsEventsManager::hook('ctype_dataset_update', array($dataset, $ctype, $this));

        cmsCache::getInstance()->clean('content.datasets');

        if(($old_dataset['sorting'] != $dataset['sorting']) || ($old_dataset['filters'] != $dataset['filters'])){

            $this->deleteContentDatasetIndex($ctype['name'], $old_dataset['index']);

            $index = $this->addContentDatasetIndex($dataset, $ctype['name']);

            $this->update('content_datasets', $id, array('index'=>$index));

            cmsCache::getInstance()->clean('content.datasets');

        }

        return $success;

    }

    public function deleteContentDataset($id){

        $dataset = $this->getContentDataset($id);
        if(!$dataset){ return false; }

        if($dataset['ctype_id']){
            $ctype = $this->getContentType($dataset['ctype_id']);
            if (!$ctype) { return false; }
        } else {
            $ctype = array(
                'title' => string_lang($dataset['target_controller'].'_controller'),
                'name'  => $dataset['target_controller'],
                'id'    => null
            );
            $this->setTablePrefix('');
        }

        cmsEventsManager::hook('ctype_dataset_before_delete', array($dataset, $ctype, $this));

        $this->delete('content_datasets', $id);

        $this->deleteContentDatasetIndex($ctype['name'], $dataset['index']);

        cmsCache::getInstance()->clean('content.datasets');

        return true;

    }

//============================================================================//
//=============================   Фильтры   ==================================//
//============================================================================//

    public function addContentFilter($filter, $ctype){

        $table_name = $this->getContentTypeTableName($ctype['name']).'_filters';

        $filter['cats'] = array_filter($filter['cats']);
        $filter['filters'] = array_filter_recursive($filter['filters']);
        array_multisort($filter['filters']);
        $filter['hash'] = md5(json_encode($filter['filters']));

        $filter['id'] = $this->insert($table_name, $filter, true);

        cmsEventsManager::hook('ctype_filter_add', array($filter, $ctype, $this));
        cmsEventsManager::hook('ctype_filter_'.$ctype['name'].'_add', array($filter, $ctype, $this));

        cmsCache::getInstance()->clean('content.filters.'.$ctype['name']);

        return $filter['id'];

    }

    public function updateContentFilter($filter, $ctype){

        list($filter, $ctype) = cmsEventsManager::hook('ctype_filter_update', array($filter, $ctype));
        list($filter, $ctype) = cmsEventsManager::hook('ctype_filter_'.$ctype['name'].'_update', array($filter, $ctype));

        $table_name = $this->getContentTypeTableName($ctype['name']).'_filters';

        $filter['cats'] = array_filter($filter['cats']);
        $filter['filters'] = array_filter_recursive($filter['filters']);
        array_multisort($filter['filters']);
        $filter['hash'] = md5(json_encode($filter['filters']));

        $this->update($table_name, $filter['id'], $filter, false, true);

        cmsCache::getInstance()->clean('content.filters.'.$ctype['name']);

        return true;

    }

    public function deleteContentFilter($ctype, $id){

        $table_name = $this->getContentTypeTableName($ctype['name']).'_filters';

        $this->delete($table_name, $id);

        cmsCache::getInstance()->clean('content.filters.'.$ctype['name']);

        return true;

    }

//============================================================================//
//===============================   СВЯЗИ   ==================================//
//============================================================================//

    public function getContentRelations($ctype_id = false) {

        if ($ctype_id) {
            $this->filterEqual('ctype_id', $ctype_id);
        }

        $this->useCache('content.relations');

        $relations = $this->get('content_relations', function ($item) {
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });

        return $relations;
    }

    public function getContentRelation($id, $by_field = 'id'){
        return $this->getItemByField('content_relations', $by_field, $id, function($item){
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });
    }

    public function addContentRelation($relation){

        cmsCache::getInstance()->clean('content.relations');

        $relation['ordering'] = $this->getNextOrdering('content_relations');

        return $this->insert('content_relations', $relation);

    }

    public function updateContentRelation($id, $relation){

        cmsCache::getInstance()->clean('content.relations');

        return $this->update('content_relations', $id, $relation);

    }

    public function deleteContentRelation($id){

        cmsCache::getInstance()->clean('content.relations');

        return $this->delete('content_relations', $id);

    }

//============================================================================//
//============================    СВОЙСТВА   =================================//
//============================================================================//

    public function isContentPropsExists($ctype_name){

        $props_table_name = $this->table_prefix . $ctype_name . '_props';

        return (bool)$this->getCount($props_table_name);
    }

    public function getContentPropsBinds($ctype_name, $category_id = false) {

        $props_table_name = $this->table_prefix . $ctype_name . '_props';
        $bind_table_name = $this->table_prefix . $ctype_name . '_props_bind';

        $this->selectOnly('p.*');
        $this->select('p.id', 'prop_id');
        $this->select('i.id', 'id');
        $this->select('i.cat_id', 'cat_id');

        $this->join($props_table_name, 'p', 'p.id = i.prop_id');

        if ($category_id){
            $this->filterEqual('i.cat_id', $category_id);
        }

        $this->orderBy('i.ordering');
        $this->groupBy('p.id');

        return $this->get($bind_table_name);
    }

    public function getContentProp($ctype_name, $id){

        $props_table_name = $this->table_prefix . $ctype_name . '_props';
        $bind_table_name = $this->table_prefix . $ctype_name . '_props_bind';

        $prop = $this->getItemById($props_table_name, $id, function($item, $model){
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });

        if(!$prop){
            return false;
        }

        $this->filterEqual('prop_id', $id);

        $prop['cats'] = $this->get($bind_table_name, function($item, $model){
           return (int)$item['cat_id'];
        });

        return $prop;
    }

    public function updateContentProp($ctype_name, $id, $prop) {

        $table_name = $this->table_prefix . $ctype_name . '_props';

        $old_prop = $this->getContentProp($ctype_name, $id);

        $missed_cats_list = array_diff($old_prop['cats'], $prop['cats']);
        $added_cats_list  = array_diff($prop['cats'], $old_prop['cats']);

        if ($missed_cats_list) {
            foreach ($missed_cats_list as $cat_id) {
                $this->unbindContentProp($ctype_name, $id, $cat_id);
            }
        }

        if ($added_cats_list) {
            $this->bindContentProp($ctype_name, $id, $added_cats_list);
        }

        unset($prop['cats']);

        $prop['id'] = $id;

        cmsEventsManager::hook('ctype_prop_before_update', [$prop, $old_prop, $ctype_name, $this]);

        $result = $this->update($table_name, $id, $prop);

        cmsEventsManager::hook('ctype_prop_after_update', [$prop, $ctype_name, $this]);

        return $result;
    }

    public function deleteContentProp($ctype_name_or_id, $prop_id){

        if (is_numeric($ctype_name_or_id)){
            $ctype = $this->getContentType($ctype_name_or_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_name_or_id;
        }

        $table_name = $this->table_prefix . $ctype_name . '_props';

        $prop = $this->getContentProp($ctype_name, $prop_id);

        cmsEventsManager::hook('ctype_prop_before_delete', array($prop, $ctype_name, $this));

        foreach($prop['cats'] as $cat_id){
            $this->unbindContentProp($ctype_name, $prop_id, $cat_id);
        }

        $this->deleteContentPropValues($ctype_name, $prop_id);

        return $this->delete($table_name, $prop_id);
    }

    public function deleteContentPropValues($ctype_name, $prop_id){

        $table_name = $this->table_prefix . $ctype_name . '_props_values';

        return $this->filterEqual('prop_id', $prop_id)->deleteFiltered($table_name);
    }

    public function addContentProp($ctype_name, $prop){

        $table_name = $this->table_prefix . $ctype_name . '_props';

        $cats_list = $prop['cats']; unset($prop['cats']);

        $prop['id'] = $this->insert($table_name, $prop);

        $this->bindContentProp($ctype_name, $prop['id'], $cats_list);

        cmsEventsManager::hook('ctype_prop_after_add', array($prop, $ctype_name, $this));

        return $prop['id'];
    }

	public function toggleContentPropFilter($ctype_name, $id, $is_in_filter){

		$table_name = $this->table_prefix . $ctype_name . '_props';

		return $this->update($table_name, $id, array(
			'is_in_filter' => $is_in_filter
		));
	}

    public function bindContentProp($ctype_name, $prop_id, $cats_list){

        $table_name = $this->table_prefix . $ctype_name . '_props_bind';

        foreach($cats_list as $cat_id){

            $this->filterEqual('cat_id', $cat_id);

            $ordering = $this->getNextOrdering($table_name);

            $this->insert($table_name, array(
                'prop_id' => $prop_id,
                'cat_id' => $cat_id,
                'ordering' => $ordering
            ));

        }

        return true;
    }

    public function unbindContentProp($ctype_name, $prop_id, $cat_id){

        $table_name = $this->table_prefix . $ctype_name . '_props_bind';

        $this->
            filterEqual('prop_id', $prop_id)->
            filterEqual('cat_id', $cat_id)->
            deleteFiltered($table_name);

        $this->
            filterEqual('cat_id', $cat_id)->
            reorder($table_name);

        return true;
    }

    public function reorderContentProps($ctype_name, $props_ids_list){

        $table_name = $this->table_prefix . $ctype_name . '_props_bind';

        return $this->reorderByList($table_name, $props_ids_list);
    }

    public function getContentPropsFieldsets($ctype_id){

        if (is_numeric($ctype_id)){
            $ctype = $this->getContentType($ctype_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_id;
        }

        $table_name = $this->table_prefix . $ctype_name . '_props';

        $this->groupBy('fieldset');
        $this->orderBy('fieldset');

        $fieldsets = $this->get($table_name, function($item, $model){
            $item = $item['fieldset'];
            return $item;
        }, false);

        if (is_array($fieldsets) && $fieldsets[0] == '') { unset($fieldsets[0]); }

        return $fieldsets;
    }

}
