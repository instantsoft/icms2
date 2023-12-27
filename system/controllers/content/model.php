<?php

class modelContent extends cmsModel {

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

        $item['options'] = cmsModel::yamlToArray($item['options']);
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

        $labels = cmsModel::yamlToArray($labels);
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
            $table_name = $this->table_prefix . $ctype['name'];
            $this->updateFiltered($table_name, [
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
//=======================    ПОЛЯ КОНТЕНТА   =================================//
//============================================================================//

    public function getDefaultContentFieldOptions() {
        return [
            'is_required'           => 0,
            'is_digits'             => 0,
            'is_number'             => 0,
            'is_alphanumeric'       => 0,
            'is_email'              => 0,
            'is_unique'             => 0,
            'is_url'                => 0,
            'disable_drafts'        => 0,
            'is_date_range_process' => 'hide',
            'label_in_list'         => 'none',
            'label_in_item'         => 'none',
            'wrap_type'             => 'auto',
            'wrap_width'            => '',
            'wrap_style'            => '',
            'profile_value'         => '',
            'is_in_item_pos'        => ['page']
        ];
    }

    public function addContentField($ctype_name, $field, $is_virtual = false) {

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name  = $this->table_prefix . $ctype_name . '_fields';

        $field['ordering'] = $this->getNextOrdering($fields_table_name);

        if (!$is_virtual){

            $field_class  = 'field'.string_to_camel('_', $field['type']);
            $field_parser = new $field_class(null, (isset($field['options']) ? ['options' => $field['options']] : null));

            $sql = "ALTER TABLE {#}{$content_table_name} ADD `{$field['name']}` {$field_parser->getSQL()}";
            $this->db->query($sql);

            $field_parser->hookAfterAdd($content_table_name, $field, $this);

            if($field_parser->is_denormalization){

                $cfield_name = $field['name'].cmsFormField::FIELD_CACHE_POSTFIX;
                $sql = "ALTER TABLE {#}{$content_table_name} ADD `{$cfield_name}` {$field_parser->getCacheSQL()}";
                $this->db->query($sql);

            }

            if (!empty($field['is_in_filter']) && $field_parser->allow_index){
                $this->db->addIndex($content_table_name, $field['name']);
            }

        }

        // Если не выбрана группа, обнуляем поля групп
        foreach ($field as $key => $value) {
            if(strpos($key, 'fieldset') === 0 && !$value){
                $field[$key] = null;
            }
        }

        // если создается новая группа, то выбираем ее
        if ($field['new_fieldset']) {
            $field[$this->getTranslatedFieldName('fieldset', $fields_table_name)] = $field['new_fieldset'];
        }

        $field['id'] = $this->insert($fields_table_name, $field);

        cmsEventsManager::hook('ctype_field_after_add', array($field, $ctype_name, $this));

        cmsCache::getInstance()->clean("content.fields.{$ctype_name}");

        // если есть опция полнотекстового поиска
        if(!$is_virtual && is_array($field['options']) && !empty($field['options']['in_fulltext_search'])){
            // получаем полнотекстовый индекс для таблицы, он может быть только один
            $fulltext_index = $this->db->getTableIndexes($content_table_name, 'FULLTEXT');
            if($fulltext_index){
                // название индекса
                $index_name = key($fulltext_index);
                // поля индекса
                $index_fields = $fulltext_index[$index_name];
                // ищем, нет ли такого поля уже в индексе, мало ли :-)
                $key = array_search($field['name'], $index_fields);
                // не нашли, добавляем
                if($key === false){
                    // удаляем старый индекс
                    $this->db->dropIndex($content_table_name, $index_name);
                    // создаем новый
                    $this->createFullTextIndex($ctype_name, $field['name']);
                }
            } else {
                $this->createFullTextIndex($ctype_name, $field['name']);
            }
        }

        return $field['id'];
    }

    public function getContentFieldsCount($ctype_name){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->useCache('content.fields.'.$ctype_name);

        return $this->getCount($table_name);
    }

    public function getContentFields($ctype_name, $item_id = false, $enabled = true, $show_fields = []) {

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->selectTranslatedField('i.values', $table_name, 'default');

        $this->useCache('content.fields.' . $ctype_name);

        if ($enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        $this->orderBy('ordering');

        $fields = $this->get($table_name, function($item, $model) use ($ctype_name, $item_id, $show_fields) {

            if($show_fields && !in_array($item['name'], $show_fields)){
                return false;
            }

            $item['options']     = cmsModel::yamlToArray($item['options']);
            $item['options']     = array_merge($model->getDefaultContentFieldOptions(), $item['options']);
            $item['groups_read'] = cmsModel::yamlToArray($item['groups_read']);
            $item['groups_add']  = cmsModel::yamlToArray($item['groups_add']);
            $item['groups_edit'] = cmsModel::yamlToArray($item['groups_edit']);
            $item['filter_view'] = cmsModel::yamlToArray($item['filter_view']);

            $item = $model->formatFieldVisibleDepend($item);

            $rules = [];
            if ($item['options']['is_required']) { $rules[] = ['required']; }
            if ($item['options']['is_digits']) { $rules[] = ['digits']; }
            if ($item['options']['is_number']) { $rules[] = ['number']; }
            if ($item['options']['is_alphanumeric']) { $rules[] = ['alphanumeric']; }
            if ($item['options']['is_email']) { $rules[] = ['email']; }
            if (!empty($item['options']['is_url'])) { $rules[] = ['url']; }
            if (!empty($item['options']['is_regexp']) && !empty($item['options']['rules_regexp_str'])) {
                $rules[] = ['regexp', $item['options']['rules_regexp_str']];
            }

            if ($item['options']['is_unique']) {
                if (!$item_id) {
                    $rules[] = ['unique', $model->table_prefix . $ctype_name, $item['name']];
                } else {
                    $rules[] = ['unique_exclude', $model->table_prefix . $ctype_name, $item['name'], $item_id];
                }
            }

            $item['rules'] = $rules;

            return $item;
        }, 'name');

        // чтобы сработала мультиязычность, если необходима
        // поэтому перебираем тут, а не выше
        if ($fields) {
            foreach ($fields as $name => $field) {

                $field_property = $field;

                $field_class = 'field' . string_to_camel('_', $field['type']);

                $field['handler'] = new $field_class($field['name']);

                $field['handler_title'] = $field['handler']->getTitle();

                unset($field_property['type']);

                $field['handler']->setOptions($field_property);

                $fields[$name] = $field;
            }
        }

        return $fields;
    }

    private function formatFieldVisibleDepend($field) {

        if(empty($field['options']['visible_depend'])){
            return $field;
        }

        $field['visible_depend'] = [];

        foreach ($field['options']['visible_depend'] as $vd) {
            $field['visible_depend'][$vd['field']] = [$vd['action'] => explode(',', ''.$vd['value'])];
        }

        unset($field['options']['visible_depend']);

        return $field;
    }

    public function getRequiredContentFields($ctype_name){

        $fields = $this->getContentFields($ctype_name);

        $req_fields = [];

        foreach($fields as $field){
            if ($field['options']['is_required']) {
                $req_fields[] = $field;
            }
        }

        return $req_fields;
    }

    public function getContentField($ctype_name, $id, $by_field = 'id'){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->useCache('content.fields.'.$ctype_name);

        return $this->getItemByField($table_name, $by_field, $id, function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);

            if (!$item['is_system']){
                $item['options'] = array_merge($model->getDefaultContentFieldOptions(), $item['options']);
            }

            $item['groups_read'] = cmsModel::yamlToArray($item['groups_read']);
            $item['groups_add']  = cmsModel::yamlToArray($item['groups_add']);
            $item['groups_edit'] = cmsModel::yamlToArray($item['groups_edit']);
            $item['filter_view'] = cmsModel::yamlToArray($item['filter_view']);

            $field_class = 'field' . string_to_camel('_', $item['type']);

            $handler = new $field_class($item['name']);

            $item['parser_title'] = $handler->getTitle();

            $handler->setOptions($item);

            $item['parser'] = $handler;

            return $item;

        });
    }

    public function getContentFieldByName($ctype_name, $name){
        return $this->getContentField($ctype_name, $name, 'name');
    }

    public function isContentFieldExists($ctype_name, $name){
        return $this->getContentField($ctype_name, $name, 'name') !== false;
    }

    public function reorderContentFields($ctype_name, $fields_ids_list){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->reorderByList($table_name, $fields_ids_list);

        cmsCache::getInstance()->clean("content.fields.{$ctype_name}");

        return true;
    }

    public function updateContentField($ctype_name, $id, $field){

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name = $this->table_prefix . $ctype_name . '_fields';

        $field_old = $this->getContentField($ctype_name, $id);

        if (!$field_old['is_system']){

            $new_lenght = ((isset($field['options']) && !empty($field['options']['max_length'])) ? $field['options']['max_length'] : false);
            $old_lenght = ((isset($field_old['options']) && !empty($field_old['options']['max_length'])) ? $field_old['options']['max_length'] : false);

            $field_class   = 'field'.string_to_camel('_', $field['type']);
            $field_handler = new $field_class(null, (isset($field['options']) ? ['options' => $field['options']] : null));

            $field_handler->hookAfterUpdate($content_table_name, $field, $field_old, $this);

            if (($field_old['name'] !== $field['name']) || ($field_old['type'] !== $field['type']) || ($new_lenght != $old_lenght)){

                if($field_old['type'] !== $field['type']){ $this->db->dropIndex($content_table_name, $field_old['name']); }

                $sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$field_old['name']}` `{$field['name']}` {$field_handler->getSQL()}";
                // Пробуем сменить
                $result = $this->db->query($sql, false, true);
                // Не получилось конвертировать (вероятно задан sql_mode в MySQL)
                if($result === false){
                    // очищаем данные
                    $this->db->query("UPDATE `{#}{$content_table_name}` SET `{$field_old['name']}` = NULL");
                    // И заново меняем
                    $this->db->query($sql);
                }

                if(($field_old['name'] !== $field['name']) || ($field_old['type'] !== $field['type'])){

                    // поля денормализации
                    $old_cfield_name = $field_old['name'].cmsFormField::FIELD_CACHE_POSTFIX;
                    $new_cfield_name = $field['name'].cmsFormField::FIELD_CACHE_POSTFIX;

                    $update_cache_sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$old_cfield_name}` `{$new_cfield_name}` {$field_handler->getCacheSQL()}";

                    // изменилось только имя поля
                    if($field_handler->is_denormalization && $field_old['type'] === $field['type']){

                        $this->db->query($update_cache_sql);

                    }
                    // изменился тип
                    if($field_old['type'] !== $field['type']){

                        if($field_old['parser']->is_denormalization && $field_handler->is_denormalization){

                            $this->db->query($update_cache_sql);

                        } elseif($field_old['parser']->is_denormalization && !$field_handler->is_denormalization){

                            $this->db->dropTableField($content_table_name, $old_cfield_name);

                        } elseif(!$field_old['parser']->is_denormalization && $field_handler->is_denormalization){

                            $sql = "ALTER TABLE {#}{$content_table_name} ADD `{$new_cfield_name}` {$field_handler->getCacheSQL()}";
                            $this->db->query($sql);

                        }

                    }

                    // удаляем старый индекс
                    $this->db->dropIndex($content_table_name, $field_old['name']);

                    // добавляем новый
                    if ($field['is_in_filter'] && $field_handler->allow_index){
                        $this->db->addIndex($content_table_name, $field['name']);
                    }

                }

                // имя поля сменилось
                if(($field_old['name'] !== $field['name'])){
                    if(!$this->table_prefix){
                        $this->filterEqual('target_controller', $ctype_name);
                    } else {
                        $ctype = $this->getContentTypeByName($ctype_name);
                        $this->filterEqual('ctype_id', $ctype['id']);
                    }
                    $this->lockFilters();
                    $this->replaceFieldString('content_datasets', "by: {$field_old['name']}", "by: {$field['name']}", 'sorting');
                    $this->unlockFilters();
                    $this->replaceFieldString('content_datasets', "field: {$field_old['name']}", "field: {$field['name']}", 'filters');
                }
            }

            if ($field['is_in_filter'] && $field_handler->allow_index && !$field_old['is_in_filter']){
                $this->db->addIndex($content_table_name, $field['name']);
            }

            if (!$field['is_in_filter'] && $field_handler->allow_index && $field_old['is_in_filter']){
                $this->db->dropIndex($content_table_name, $field_old['name']);
            }

            // если есть опция полнотекстового поиска и ее значение изменилось
            if(is_array($field['options']) && array_key_exists('in_fulltext_search', $field['options'])){
                if($field['options']['in_fulltext_search'] != @$field_old['options']['in_fulltext_search']){
                    // получаем полнотекстовый индекс для таблицы, он может быть только один
                    $fulltext_index = $this->db->getTableIndexes($content_table_name, 'FULLTEXT');
                    if($fulltext_index){
                        // название индекса
                        $index_name = key($fulltext_index);
                        // поля индекса
                        $index_fields = $fulltext_index[$index_name];
                        // выключили опцию
                        if(!$field['options']['in_fulltext_search']){
                            $key = array_search($field['name'], $index_fields);
                            // нашли - удаляем из массива
                            if($key !== false){
                                unset($index_fields[$key]);
                                // удаляем индекс
                                $this->db->dropIndex($content_table_name, $index_name);
                                // и создаем новый
                                if($index_fields){
                                    $this->db->addIndex($content_table_name, $index_fields, '', 'FULLTEXT');
                                }
                            }
                        }
                        // включили опцию
                        if($field['options']['in_fulltext_search']){
                            // ищем, нет ли такого поля уже в индексе, мало ли :-)
                            $key = array_search($field['name'], $index_fields);
                            // не нашли, добавляем
                            if($key === false){
                                // удаляем старый индекс
                                $this->db->dropIndex($content_table_name, $index_name);
                                // создаем новый
                                $this->createFullTextIndex($ctype_name, $field['name']);
                            }
                        }

                    } elseif($field['options']['in_fulltext_search']) {
                        $this->createFullTextIndex($ctype_name, $field['name']);
                    }
                }
            }

        }

        // Если не выбрана группа, обнуляем поля групп
        foreach ($field as $key => $value) {
            if(strpos($key, 'fieldset') === 0 && !$value){
                $field[$key] = null;
            }
        }

        // если создается новая группа, то выбираем ее
        if ($field['new_fieldset']) {
            $field[$this->getTranslatedFieldName('fieldset', $fields_table_name)] = $field['new_fieldset'];
        }

        $result = $this->update($fields_table_name, $id, $field);

        if ($result){
            $field['id'] = $id;
            cmsEventsManager::hook('ctype_field_after_update', [$field, $ctype_name, $this]);
            cmsEventsManager::hook('ctype_field_'.str_replace(['{','}'], '', $ctype_name).'_after_update', [$field, $this]);
        }

        cmsCache::getInstance()->clean('content.fields.'.$ctype_name);
        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

        return $result;
    }

    /**
     * Создает fulltext индекс согласно настроек полей типа контента
     * @param string $ctype_name Название типа контента
     * @param string|null $add_field Название поля, для которого принудительно нужно создать индекс
     * @return boolean
     */
    public function createFullTextIndex($ctype_name, $add_field = null) {

        // важен порядок индексов, поэтому создаем их так, как они будут в запросе
        // для этого получаем все поля этого типа контента
        $fields = $this->getContentFields($ctype_name);

        foreach ($fields as $field) {

            $is_text = $field['handler']->getOption('in_fulltext_search') || $field['name'] === $add_field;
            if(!$is_text){ continue; }

            $index_fields[] = $field['name'];

        }

        if($index_fields){

            $this->db->addIndex($this->table_prefix . $ctype_name, $index_fields, '', 'FULLTEXT');

            return true;
        }

        return false;
    }

	public function toggleContentFieldVisibility($ctype_name, $id, $mode, $is_visible){

		$fields_table_name = $this->table_prefix . $ctype_name . '_fields';

		$result = $this->update($fields_table_name, $id, array(
			$mode => $is_visible
		));

        cmsCache::getInstance()->clean("content.fields.{$ctype_name}");

        return $result;
	}

    public function deleteContentField($ctype_name_or_id, $id, $by_field = 'id', $is_forced = false) {

        if (is_numeric($ctype_name_or_id)) {
            $ctype      = $this->getContentType($ctype_name_or_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_name_or_id;
        }

        $field = $this->getContentField($ctype_name, $id, $by_field);
        if ($field['is_fixed'] && !$is_forced) {
            return false;
        }

        cmsEventsManager::hook('ctype_field_before_delete', [$field, $ctype_name, $this]);

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name  = $this->table_prefix . $ctype_name . '_fields';

        $this->delete($fields_table_name, $id, $by_field);
        $this->reorder($fields_table_name);

        cmsCache::getInstance()->clean("content.fields.{$ctype_name}");

        if (!$field['parser']->is_virtual) {
            $this->db->dropTableField($content_table_name, $field['name']);
        }

        $field['parser']->hookAfterRemove($content_table_name, $field, $this);

        if ($field['parser']->is_denormalization) {
            $this->db->dropTableField($content_table_name, $field['parser']->getDenormalName());
        }

        return true;
    }

    public function getContentFieldsets($ctype_id) {

        if (is_numeric($ctype_id)) {
            $ctype      = $this->getContentType($ctype_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_id;
        }

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->useCache('content.fields.' . $ctype_name);

        $name = $this->getTranslatedFieldName('fieldset', $table_name);

        if (!$this->order_by) {
            $this->orderBy($name);
        }

        $this->groupBy($name);

        $this->selectOnly($name, 'fieldset');

        return $this->get($table_name, function ($item, $model) {

            if(!$item['fieldset']){
                return false;
            }
            return $item['fieldset'];
        }, false) ?: [];
    }

//============================================================================//
//============================    СВОЙСТВА   =================================//
//============================================================================//

    public function getContentProps($ctype_name, $category_id = false) {

        $props_table_name = $this->table_prefix . $ctype_name . '_props';
        $bind_table_name  = $this->table_prefix . $ctype_name . '_props_bind';

        if ($category_id) {
            $this->selectOnly('p.*');
            $this->select('c.title', 'cat_title');
            $this->select('i.cat_id');
            $this->join($props_table_name, 'p', 'p.id = i.prop_id');
            $this->join($this->table_prefix . $ctype_name . '_cats', 'c', 'c.id = i.cat_id');
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
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });
    }

    public function unbindContentProps($ctype_name, $cat_id) {

        $table_name = $this->table_prefix . $ctype_name . '_props_bind';

        return $this->filterEqual('cat_id', $cat_id)->
            deleteFiltered($table_name);
    }

    public function getPropsValues($ctype_name, $item_id) {

        $table_name = $this->table_prefix . $ctype_name . '_props_values';

        $this->filterEqual('item_id', $item_id);

        return $this->get($table_name, function ($item, $model) {
            return $item['value'];
        }, 'prop_id');
    }

    public function addPropsValues($ctype_name, $item_id, $props_values) {

        $table_name = $this->table_prefix . $ctype_name . '_props_values';

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

        $table_name = $this->table_prefix . $ctype_name . '_props_values';

        $props_ids = array_keys($props_values);

        $this->filterEqual('item_id', $item_id)->
            filterIn('prop_id', $props_ids)->
            deleteFiltered($table_name);

        return $this->addPropsValues($ctype_name, $item_id, $props_values);
    }

    public function deletePropsValues($ctype_name, $item_id) {

        $table_name = $this->table_prefix . $ctype_name . '_props_values';

        return $this->filterEqual('item_id', $item_id)->
            deleteFiltered($table_name);
    }

//============================================================================//
//===============================   СВЯЗИ   ==================================//
//============================================================================//

    public function getContentRelationByTypes($ctype_id, $child_ctype_id, $target_controller = 'content') {

        $this->filterEqual('ctype_id', $ctype_id);
        $this->filterEqual('child_ctype_id', $child_ctype_id);
        $this->filterEqual('target_controller', $target_controller);

        return $this->getItem('content_relations', function ($item) {
            $item['options'] = cmsModel::yamlToArray($item['options']);
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

            $item['child_labels'] = cmsModel::yamlToArray($item['child_labels']);
            $item['options']      = cmsModel::yamlToArray($item['options']);
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

                $item_table = $this->table_prefix . $relation['child_ctype_name'];

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

        $parent_ctype_table = $this->table_prefix . $parent_ctype['name'];

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

        $table_name = $this->getContentTypeTableName($ctype_name).'_filters';

        return $this->get($table_name, function($item, $model){

            $item['cats'] = cmsModel::stringToArray($item['cats']);

            if(isset($item['filters'])){
                $item['filters'] = cmsModel::stringToArray($item['filters']);
            }

            return $item;
        });
    }

    public function getContentFilter($ctype, $id, $by_hash = false, $cat_id = 0) {

        if (!$this->isFiltersTableExists($ctype['name'])) {
            return false;
        }

        $table_name = $this->getContentTypeTableName($ctype['name']) . '_filters';

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

            $item['cats']       = cmsModel::stringToArray($item['cats']);
            $item['filters']    = cmsModel::stringToArray($item['filters']);
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

            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            $item['cats_view']   = cmsModel::yamlToArray($item['cats_view']);
            $item['cats_hide']   = cmsModel::yamlToArray($item['cats_hide']);
            $item['filters']     = $item['filters'] ? cmsModel::yamlToArray($item['filters']) : [];
            $item['sorting']     = $item['sorting'] ? cmsModel::yamlToArray($item['sorting']) : [];
            $item['list']        = cmsModel::stringToArray($item['list']);

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

            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            $item['cats_view']   = cmsModel::yamlToArray($item['cats_view']);
            $item['cats_hide']   = cmsModel::yamlToArray($item['cats_hide']);
            $item['filters']     = $item['filters'] ? cmsModel::yamlToArray($item['filters']) : [];
            $item['sorting']     = $item['sorting'] ? cmsModel::yamlToArray($item['sorting']) : [];
            $item['list']        = cmsModel::stringToArray($item['list']);

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

		$table_name = $this->getContentTypeTableName($ctype_name).'_filters';

        return $this->db->isTableExists($table_name);

    }

    public function filterPropValue($ctype_name, $prop, $value){

        $table_name  = $this->table_prefix.$ctype_name.'_props_values';
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

	public function getContentItemCategories($ctype_name, $id){

		$table_name = $this->table_prefix . $ctype_name . '_cats_bind';

		return $this->filterEqual('item_id', $id)->get($table_name, function($item, $model){
			return $item['category_id'];
		}, false);

	}

	public function getContentItemCategoriesList($ctype_name, $id){

		$bind_table_name = $this->table_prefix . $ctype_name . '_cats_bind';
        $cats_table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->join($bind_table_name, 'b', 'b.category_id = i.id');

        $this->filterEqual('b.item_id', $id);

        $this->orderBy('ns_left');

        $this->useCache('content.categories');

		return $this->get($cats_table_name);

	}

    public function moveContentItemsToCategory($ctype, $from_id, $to_id, $items_ids, $fields) {

        $table_name = $this->table_prefix . $ctype['name'];
        $binds_table_name = $this->table_prefix . $ctype['name'] . '_cats_bind';

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

	public function updateContentItemCategories($ctype_name, $id, $category_id, $add_cats){

		$table_name = $this->table_prefix . $ctype_name . '_cats_bind';

		$new_cats = empty($add_cats) ? array() : $add_cats;

		if (!$category_id) { $category_id = 1; }

		if (!in_array($category_id, $new_cats)){
			$new_cats[] = $category_id;
		}

		$current_cats = $this->
							filterEqual('item_id', $id)->
							get($table_name, function($item, $model){
								return $item['category_id'];
							}, false);

		if ($current_cats){
			foreach($current_cats as $current_cat_id){

				if (!in_array($current_cat_id, $new_cats)){
					$this->
						filterEqual('item_id', $id)->
						filterEqual('category_id', $current_cat_id)->
						deleteFiltered($table_name);
				}

			}
		}

		foreach($new_cats as $new_cat_id){
			if (!$current_cats || !in_array($new_cat_id, $current_cats)){
				$this->insert($table_name, array(
					'item_id' => $id,
					'category_id' => $new_cat_id
				));
			}
		}

	}

//============================================================================//
//============================================================================//

    public function restoreContentItem($ctype_name, $id) {

        $table_name = $this->table_prefix . $ctype_name;

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

        $table_name = $this->table_prefix . $ctype_name;

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

        $table_name = $this->table_prefix . $ctype_name;

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

        $table_name = $this->table_prefix . $ctype_name;

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }
        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }
        if (!$this->pub_filter_disabled) { $this->filterPublishedOnly(); }
        if (!$this->hidden_parents_filter_disabled) { $this->filterHiddenParents(); }

        $this->useCache("content.list.{$ctype_name}");

        return $this->getCount($table_name);
    }

    public function getContentItemsForSitemap($ctype_name, $fields = array()) {

        $table_name = $this->table_prefix . $ctype_name;

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
                'privacy_options' => cmsModel::yamlToArray($item['user_privacy_options']),
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
            if (!$ctype) { return false; }

            $ctype_name = $ctype['name'];
        }

        $table_name = $this->table_prefix . $ctype_name;

        $this->select('f.title', 'folder_title');

        $this->joinUser();
        $this->joinLeft('content_folders', 'f', 'f.id = i.folder_id');

        $this->useCache("content.item.{$ctype_name}");

        return $this->getItemByField($table_name, $by_field, $id, function ($item, $model) use ($ctype_name) {

            $item['user'] = [
                'id'              => $item['user_id'],
                'groups'          => $item['user_groups'],
                'slug'            => $item['user_slug'],
                'nickname'        => $item['user_nickname'],
                'privacy_options' => cmsModel::yamlToArray($item['user_privacy_options']),
                'avatar'          => $item['user_avatar']
            ];

            $item['is_draft'] = false;

            if (!$item['is_approved']) {
                $item['is_draft'] = $model->isDraftContentItem($ctype_name, $item);
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

    public function deleteCategory($ctype_name, $id, $is_delete_content=false){

        $category = $this->getCategory($ctype_name, $id);

        $this->filterCategory($ctype_name, $category, true, true);

        if (!$is_delete_content){
            $table_name = $this->table_prefix . $ctype_name;
            $this->updateFiltered($table_name, array(
                'category_id' => 1
            ));
        }

        if ($is_delete_content){

            $this->disableDeleteFilter()->disableApprovedFilter()->
                    disablePubFilter()->disablePrivacyFilter();

            $items = $this->getContentItems($ctype_name);

            if ($items){
                foreach($items as $item){
                    $this->deleteContentItem($ctype_name, $item['id']);
                }
            }

        }

        $this->unbindContentProps($ctype_name, $id);

        parent::deleteCategory($ctype_name, $id);

    }

//============================================================================//
//============================================================================//

    public function getRatingTarget($ctype_name, $id){

        $table_name = $this->table_prefix . $ctype_name;

        $item = $this->getItemById($table_name, $id);

        if($item){
            $item['page_url'] = href_to($ctype_name, $item['slug'].'.html');
        }

        return $item;

    }

    public function updateRating($ctype_name, $id, $rating){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array('rating' => $rating));

        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

    }

//============================================================================//
//============================================================================//

    public function updateCommentsCount($ctype_name, $id, $comments_count){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array('comments' => $comments_count));

        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

        return true;

    }

    public function getCommentsOptions($ctype_name) {

        $ctype = $this->getContentTypeByName($ctype_name);

        return [
            'enable' => $ctype['is_comments'],
            'title_pattern' => (!empty($ctype['options']['comments_title_pattern']) ? $ctype['options']['comments_title_pattern'] : ''),
            'labels' => (!empty($ctype['options']['comments_labels']) ? $ctype['options']['comments_labels'] : []),
            'template' => (!empty($ctype['options']['comments_template']) ? $ctype['options']['comments_template'] : '')
        ];

    }

    public function getTargetItemInfo($ctype_name, $id){

        $item = $this->getContentItem($ctype_name, $id);

        if (!$item){ return false; }

        return array(
            'url' => href_to_rel($ctype_name, $item['slug'].'.html'),
            'title' => $item['title'],
            'is_private' => $item['is_private'] || $item['is_parent_hidden']
        );

    }

//============================================================================//
//============================================================================//

    public function toggleParentVisibility($parent_type, $parent_id, $is_hidden){

        $ctypes_names = $this->getContentTypesNames();

        $is_hidden = $is_hidden ? 1 : null;

        foreach($ctypes_names as $ctype_name){

            $table_name = $this->table_prefix . $ctype_name;

            $this->
                filterEqual('parent_type', $parent_type)->
                filterEqual('parent_id', $parent_id)->
                updateFiltered($table_name, array('is_parent_hidden' => $is_hidden));

            cmsCache::getInstance()->clean('content.list.'.$ctype_name);
            cmsCache::getInstance()->clean('content.item.'.$ctype_name);

        }

    }

    public function isDraftContentItem($ctype_name, $item) {

        if(!empty($item['is_approved'])){ return false; }

        return !(bool)$this->selectOnly('id')->filterEqual('ctype_name', $ctype_name)->
                    filterEqual('item_id', $item['id'])->getItem('moderators_tasks');

    }

    public function getDraftCounts($user_id){

        $counts = array();

        $ctypes = $this->getContentTypes();

        foreach($ctypes as $ctype){

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
                $counts[ $ctype['name'] ] = $count;
            }

        }

        return $counts;

    }

    public function approveContentItem($ctype_name, $id, $moderator_user_id){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array(
            'is_approved'   => 1,
            'approved_by'   => $moderator_user_id,
            'date_approved' => ''
        ));

        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

        return true;

    }

    public function unbindParent($ctype_name, $id){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array(
            'parent_id'        => null,
            'parent_type'      => null,
            'parent_title'     => null,
            'parent_url'       => null,
            'is_parent_hidden' => null
        ));

        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

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
