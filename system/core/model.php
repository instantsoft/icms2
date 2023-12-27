<?php
class cmsModel {

    public $name;

    /**
     * Объект базы данных
     * @var \cmsDatabase
     */
    public $db;

    /**
     * Типы MySQL JOIN
     */
    const LEFT_JOIN                = 'LEFT JOIN';
    const RIGHT_JOIN               = 'RIGHT JOIN';
    const INNER_JOIN               = 'INNER JOIN';
    const STRAIGHT_JOIN            = 'STRAIGHT_JOIN';
    const NATURAL_LEFT_JOIN        = 'NATURAL LEFT JOIN';
    const NATURAL_RIGHT_JOIN       = 'NATURAL RIGHT JOIN';

    /**
     * Уровни изоляций транзакций
     */
    const READ_UNCOMMITTED = 'READ UNCOMMITTED';
    const READ_COMMITTED   = 'READ COMMITTED';
    const REPEATABLE_READ  = 'REPEATABLE READ';
    const SERIALIZABLE     = 'SERIALIZABLE';

    /**
     * Булевы операторы, которых быть не должно при fulltext search
     * https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
     *
     * @var array
     */
    protected $special_chars = ['+', '-', '>','<', '(', ')', '~', '*', '"', '@'];

    /**
     * Префикс по умолчанию таблиц контента
     */
    const DEFAULT_TABLE_PREFIX = 'con_';
    /**
     * Постфикс по умолчанию таблиц категорий контента
     */
    const DEFAULT_TABLE_CATEGORY_POSTFIX = '_cats';

    /**
     * Префикс таблиц контента
     * @var string
     */
    public $table_prefix = self::DEFAULT_TABLE_PREFIX;

    /**
     * Постфикс таблиц категорий контента
     */
    public $table_category_postfix = self::DEFAULT_TABLE_CATEGORY_POSTFIX;

    //условия для выборок
    public $table      = '';
    public $select     = ['i.*'];
    public $distinct   = '';
    public $straight_join = '';
    public $join       = '';
    public $where      = '';
    public $where_separator  = 'AND';
    public $group_by   = '';
    public $order_by   = '';
    public $read_type  = '';
    public $index_action = '';
    public $limit      = 1000;
    public $perpage    = 50;
    public $encoded_fields = [];

    public $keep_filters = false;
    public $filter_on  = false;

    protected static $global_localized = false;

    protected $localized = false;
    protected $privacy_filter_disabled = false;
    protected $privacy_filtered = false;
    protected $privacy_filter_value = 0;
    protected $approved_filter_disabled = false;
    protected $hidden_parents_filter_disabled = true;
    protected $delete_filter_disabled = false;
    protected $approved_filtered = false;
    protected $available_filtered = false;
    protected $hp_filtered = false;
    protected $joined_session_online = [];

    protected static $cached = [];

    protected $cache_key = false;

    protected $lang;
    protected $default_lang;

    public function __construct($db = null) {

        $this->name = strtolower(str_replace('model', '', get_called_class()));

        $this->db = $db ?? cmsCore::getInstance()->db;

        $this->lang         = cmsCore::getLanguageName();
        $this->default_lang = cmsConfig::get('language');

        if (cmsConfig::getInstance()->isfindLocalized()) {
            self::globalLocalizedOn();
        }

        $this->localized = self::$global_localized;
    }

//============================================================================//
//============================================================================//

    public function useCache($key){
        $this->cache_key = $key; return $this;
    }

    protected function stopCache(){
        $this->cache_key = false;  return $this;
    }

//============================================================================//
//============================================================================//

    public function getContentTypeTableName($name){
        return $this->table_prefix . $name;
    }

    public function setTablePrefix($prefix){
        $this->table_prefix = $prefix;
        return $this;
    }

    public function setTableCategoryPostfix($postfix){
        $this->table_category_postfix = $postfix;
        return $this;
    }

    public function getContentCategoryTableName($name){
        return $this->getContentTypeTableName($name).$this->table_category_postfix;
    }

    public function checkCorrectEqualSlug($table_name, $slug, $item_id, $max_len = 255) {

        $get_scount = function($slug) use($item_id, $table_name){
            return $this->filterNotEqual('id', $item_id)->
                filterLike('slug', $slug)->
                getCount($table_name, 'id', true);
        };

        if($get_scount($slug)){
            if(mb_strlen($slug) >= $max_len){
                $slug = mb_substr($slug, 0, ($max_len - 1));
            }
            $i = 2;
            while($get_scount($slug.$i)){
                $i++;
                if(mb_strlen($slug.$i) > $max_len){
                    $slug = mb_substr($slug, 0, ($max_len - strlen($i)));
                }
            }
            $slug .= $i;
        }

        return $slug;
    }

//============================================================================//
//============================================================================//

    public function getRootCategory($ctype_name) {
        return $this->db->getFields($this->getContentCategoryTableName($ctype_name), 'parent_id=0');
    }

    public function getCategory($ctype_name, $id, $by_field = 'id', $array_fields = ['allow_add']) {

        $this->useCache('content.categories');

        $category = $this->getItemByField($this->getContentCategoryTableName($ctype_name), $by_field, $id);
        if (!$category) { return false; }

        $category['path'] = $this->getCategoryPath($ctype_name, $category, $array_fields);

        if($array_fields){
            foreach ($array_fields as $array_field) {
                if (!empty($category[$array_field])) {
                    $category[$array_field] = cmsModel::yamlToArray($category[$array_field]);
                }
            }
        }

        return $category;
    }

    public function getCategoryBySLUG($ctype_name, $slug) {
        return $this->getCategory($ctype_name, $slug, 'slug');
    }

    public function getCategorySLUG($category, $ctype_name) {

        if (!empty($category['path'])) {

            $slug = '';

            foreach ($category['path'] as $c) {
                if ($c['id'] == 1) {
                    continue;
                }
                if ($slug) {
                    $slug .= '/';
                }
                $slug .= lang_slug(empty($c['slug_key']) ? str_replace('/', '', $c['title']) : $c['slug_key']);
            }
        } else {

            $slug = lang_slug(empty($category['slug_key']) ? str_replace('/', '', $category['title']) : $category['slug_key']);
        }

        return $this->checkCorrectEqualSlug($this->getContentCategoryTableName($ctype_name), $slug, $category['id'], 255);
    }

//============================================================================//
//============================================================================//

    public function getCategoriesTree($ctype_name, $is_show_root = true) {

        if (!$is_show_root) {
            $this->filterGt('parent_id', 0);
        }

        if (!$this->order_by) {
            $this->orderBy('ns_left');
        }

        $this->useCache('content.categories');

        return $this->get($this->getContentCategoryTableName($ctype_name), function ($node, $model) {
            if ($node['ns_level'] == 0) {
                $node['title'] = LANG_ROOT_CATEGORY;
            }
            if (!empty($node['allow_add'])) {
                $node['allow_add'] = cmsModel::yamlToArray($node['allow_add']);
            }
            return $node;
        });
    }

    public function getSubCategories($ctype_name, $parent_id = 1, $item_callback = false) {

        $this->filterEqual('parent_id', $parent_id);
        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($this->getContentCategoryTableName($ctype_name), $item_callback);
    }

    public function getSubCategoriesTree($ctype_name, $parent_id = 1, $level = 1) {

        $parent = $this->getCategory($ctype_name, $parent_id);

        $this->filterGt('ns_left', $parent['ns_left'])->
            filterLt('ns_right', $parent['ns_right']);

        if ($level) {
            $this->filterLtEqual('ns_level', $parent['ns_level'] + $level);
        }

        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($this->getContentCategoryTableName($ctype_name));
    }

//============================================================================//
//============================================================================//

    public function getCategoryPath($ctype_name, $category, $array_fields = []) {

        if (!isset($category['ns_left'])){
            $category = $this->getCategory($ctype_name, $category['id']);
        }

        $this->
            filterLtEqual('ns_left', $category['ns_left'])->
            filterGtEqual('ns_right', $category['ns_right'])->
            filterLtEqual('ns_level', $category['ns_level'])->
            filterGt('ns_level', 0)->
            orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($this->getContentCategoryTableName($ctype_name), function($item, $model) use($array_fields) {
            if($array_fields){
                foreach ($array_fields as $array_field) {
                    if (!empty($item[$array_field])) {
                        $item[$array_field] = cmsModel::yamlToArray($item[$array_field]);
                    }
                }
            }
            return $item;
        });
    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет категорию
     *
     * @param string $ctype_name Префикс таблицы категорий
     * @param array $category Массив данных категории
     * @param boolean $first_level_slug Формировать урл только первого уровня
     * @return array
     */
    public function addCategory($ctype_name, $category, $first_level_slug = false) {

        $table_name = $this->getContentCategoryTableName($ctype_name);

        $this->db->nestedSets->setTable($table_name);

        $category['id'] = $this->db->nestedSets->addNode($category['parent_id']);

        if (!$category['id']) {
            return false;
        }

        $this->update($table_name, $category['id'], $category);

        if(!$first_level_slug){
            $category['path'] = $this->getCategoryPath($ctype_name, $category);
        }

        $category['slug'] = $this->getCategorySLUG($category, $ctype_name);

        $this->update($table_name, $category['id'], [
            'slug' => $category['slug']
        ]);

        cmsCache::getInstance()->clean('content.categories');

        return $category;
    }

    /**
     * Обновляет данные категории
     *
     * @param string $ctype_name Префикс таблицы категорий
     * @param integer $id ID категории
     * @param array $category Массив данных категории
     * @param boolean $first_level_slug Формировать урл только первого уровня
     * @return array
     */
    public function updateCategory($ctype_name, $id, $category, $first_level_slug = false) {

        $table_name = $this->getContentCategoryTableName($ctype_name);

        $category_old = $this->getCategory($ctype_name, $id);

        if ($category_old['parent_id'] != $category['parent_id']) {
            $this->db->nestedSets->setTable($table_name);
            $this->db->nestedSets->moveNode($id, $category['parent_id']);
        }

        $this->update($table_name, $id, $category);

        cmsCache::getInstance()->clean('content.categories');

        // Если текущий язык не по умолчанию, не пытаемся менять slug
        if ($this->lang !== $this->default_lang) {

            return $this->getCategory($ctype_name, $id);
        }

        $category['id'] = $id;

        if(!$first_level_slug){
            $category['path'] = $this->getCategoryPath($ctype_name, ['id' => $id]);
        }

        $category['slug'] = $this->getCategorySLUG($category, $ctype_name);

        $this->update($table_name, $id, [
            'slug' => $category['slug']
        ]);

        cmsCache::getInstance()->clean('content.categories');

        $subcats = $this->getSubCategoriesTree($ctype_name, $id, false);

        if ($subcats) {
            foreach ($subcats as $subcat) {

                if(!$first_level_slug){
                    $subcat['path'] = $this->getCategoryPath($ctype_name, ['id' => $subcat['id']]);
                }

                $subcat['slug'] = $this->getCategorySLUG($subcat, $ctype_name);
                $this->update($table_name, $subcat['id'], ['slug' => $subcat['slug']]);

                cmsCache::getInstance()->clean('content.categories');
            }
        }

        return $category;
    }

    public function updateCategoryTree($ctype_name, $tree, $categories_count, $first_level_slug = false) {

        cmsCache::getInstance()->clean('content.categories');

        $this->updateCategoryTreeNode($ctype_name, $tree);
        $this->updateCategoryTreeNodeSlugs($ctype_name, $tree, $first_level_slug);

        $root_keys = [
            'ns_left'  => 1,
            'ns_right' => 1 + ($categories_count * 2) + 1
        ];

        return $this->update($this->getContentCategoryTableName($ctype_name), 1, $root_keys);
    }

    public function updateCategoryTreeNode($ctype_name, $tree) {

        $table_name = $this->getContentCategoryTableName($ctype_name);

        foreach ($tree as $node) {

            $this->update($table_name, $node['key'], [
                'parent_id' => $node['parent_key'],
                'ns_left'   => $node['left'],
                'ns_right'  => $node['right'],
                'ns_level'  => $node['level'],
            ]);

            if (!empty($node['children'])) {
                $this->updateCategoryTreeNode($ctype_name, $node['children']);
            }
        }

        return true;
    }

    /**
     * Перегенерирует slug у всего дерева категорий
     *
     * @param string $ctype_name Префикс таблицы категорий
     * @param array $tree Дерево категорий
     * @param boolean $first_level_slug Формировать урл только первого уровня
     *
     * @return array
     */
    public function updateCategoryTreeNodeSlugs($ctype_name, $tree, $first_level_slug = false) {

        $table_name = $this->getContentCategoryTableName($ctype_name);

        foreach ($tree as $node) {

            if(!$first_level_slug){

                $path = $this->getCategoryPath($ctype_name, [
                    'id'        => $node['key'],
                    'parent_id' => $node['parent_key'],
                    'ns_left'   => $node['left'],
                    'ns_right'  => $node['right'],
                    'ns_level'  => $node['level']
                ]);
            } else {
                $path = [];
            }

            $slug = $this->getCategorySLUG([
                'path'  => $path,
                'title' => $node['title'],
                'slug_key' => $node['slug_key'],
                'id'    => $node['key']
            ], $ctype_name);

            $this->update($table_name, $node['key'], [
                'slug' => $slug
            ]);

            if (!empty($node['children'])) {
                $this->updateCategoryTreeNodeSlugs($ctype_name, $node['children'], $first_level_slug);
            }
        }

        return true;
    }

//============================================================================//
//============================================================================//

    public function deleteCategory($ctype_name, $id){

        //
        // Эта функция должна быть переопределена и вызываться
        // из дочернего класса чтобы после нее удалять все записи
        // из категории
        //

        $table_name = $this->getContentCategoryTableName($ctype_name);

        $this->db->nestedSets->setTable($table_name);
        $this->db->nestedSets->deleteNode($id);

        cmsCache::getInstance()->clean('content.categories');

        return true;

    }

//============================================================================//
//============================================================================//

    public function delete($table_name, $id, $by_field='id'){
        $this->filterEqual($by_field, $id);
        return $this->deleteFiltered($table_name);
    }

    public function deleteFiltered($table_name){
        $where = $this->where;
        $this->resetFilters();
        return $this->db->delete($table_name, $where);
    }

//============================================================================//
//============================================================================//

    public function update($table_name, $id, $data, $skip_check_fields = false, $array_as_json = false){
        $this->filterEqual('id', $id);
        return $this->updateFiltered($table_name, $data, $skip_check_fields, $array_as_json);
    }

    public function updateFiltered($table_name, $data, $skip_check_fields = false, $array_as_json = false){
        $where = $this->where;
        $this->resetFilters();
        return $this->db->update($table_name, $where, $data, $skip_check_fields, $array_as_json);
    }

//============================================================================//
//============================================================================//

    public function insert($table_name, $data, $array_as_json = false, $ignore = false){
        return $this->db->insert($table_name, $data, false, $array_as_json, $ignore);
    }

    public function insertOrUpdate($table_name, $insert_data, $update_data = false){
        return $this->db->insertOrUpdate($table_name, $insert_data, $update_data);
    }

//============================================================================//
//============================================================================//

    public function replaceFieldString($table_name, $search, $replace, $field) {

        $this->filterLike($field, '%'.$search.'%');

        $where = $this->where;
        $this->resetFilters();

        $search  = $this->db->escape($search);
        $replace = $this->db->escape($replace);

        return $this->db->query("UPDATE `{#}{$table_name}` i SET i.{$field} = REPLACE(i.{$field}, '{$search}', '{$replace}') WHERE {$where}");
    }

//============================================================================//
//============================================================================//

    public function lockFilters(){
        $this->keep_filters = true;
        return $this;
    }

    public function unlockFilters(){
        $this->keep_filters = false;
        return $this;
    }

    public function resetFilters(){

        $this->select       = ['i.*'];
        $this->encoded_fields = [];
        $this->group_by     = '';
        $this->order_by     = '';
        $this->index_action = '';
        $this->limit        = '';
        $this->read_type    = '';
        $this->join         = '';
        $this->distinct     = '';
        $this->straight_join = '';
        $this->joined_session_online = [];

        if ($this->keep_filters) { return $this; }

        $this->filter_on          = false;
        $this->where              = '';
        $this->privacy_filtered   = false;
        $this->privacy_filter_value = 0;
        $this->approved_filtered  = false;
        $this->available_filtered = false;
        $this->hp_filtered        = false;

        return $this;
    }

    public function setLang($lang) {

        $this->lang = $lang;

        return $this;
    }

    public function isLocalizedOn() {
        return $this->localized;
    }

    public function localizedOn() {
        $this->localized = true; return $this;
    }

    public function localizedOff() {
        $this->localized = false; return $this;
    }

    public function localizedRestore() {
        $this->localized = self::$global_localized; return $this;
    }

    public static function globalLocalizedOn() {
        self::$global_localized = true;
    }

    public static function globalLocalizedOff() {
        self::$global_localized = false;
    }

    public function replaceTranslatedField($item, $table_name = false) {

        // предполагается, что язык в настройках -
        // основной язык и основные тексты хранятся
        // в ячейках без постфикса
        if ($this->lang === $this->default_lang) {
            return $item;
        }

        if (!is_array($item)) {
            return $item;
        }

        $postfix = '_' . $this->lang;

        foreach ($item as $key => $value) {

            $lang_key = $key . $postfix;

            if (!isset($item[$lang_key])) {

                if (is_array($value) && $value) {
                    $item[$key] = $this->replaceTranslatedField($value, $table_name);
                }

                continue;
            }

            $item[$key] = $item[$lang_key];
        }

        return $item;
    }

    public function setStraightJoin() {
        $this->straight_join = self::STRAIGHT_JOIN; return $this;
    }

    public function distinctSelect() {
        $this->distinct = 'DISTINCT'; return $this;
    }

    public function filter($condition){
        if ($this->filter_on){
            $this->where .= ' '.$this->where_separator.' ('.$condition.')';
        } else {
            $this->where .= '('.$condition.')';
            $this->filter_on = true;
        }
        $this->where_separator = ' AND ';
        return $this;
    }

    public function filterStart(){
        if ($this->filter_on){
            $this->where .= ' '.$this->where_separator.' (';
        } else {
            $this->where .= '(';
        }
        $this->filter_on = false;
        return $this;
    }

    public function filterEnd(){
        $this->where .= ' ) ';
        return $this;
    }

    public function filterAnd(){
        $this->where_separator = ' AND ';
        return $this;
    }

    public function filterOr(){
        $this->where_separator = ' OR ';
        return $this;
    }

    public function filterNotNull($field){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $this->filter($field.' IS NOT NULL');
        return $this;
    }

    public function filterIsNull($field){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $this->filter($field.' IS NULL');
        return $this;
    }

    public function filterEqual($field, $value, $binary = false){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_null($value)){
            $this->filter($field.' IS NULL');
        } else {
            $value = $this->db->escape($value);
            $this->filter(($binary ? ' BINARY ' : '')."$field = '$value'");
        }
        return $this;
    }

    public function filterFunc($field, $value, $sign='='){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $this->filter("$field {$sign} $value");
        return $this;
    }

    public function filterNotEqual($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_null($value)){
            $this->filter($field.' IS NOT NULL');
        } else {
            $value = $this->db->escape($value);
            $this->filter("$field <> '$value'");
        }
        return $this;
    }

    /**
     * Фильтр "поле > значение"
     *
     * @param string $field Имя поля
     * @param mixed $value Значение
     * @return $this
     */
    public function filterGt($field, $value) {
        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }
        $value = $this->db->escape($value);
        $this->filter("$field > '$value'");
        return $this;
    }

    /**
     * Фильтр "поле < значение"
     *
     * @param string $field Имя поля
     * @param mixed $value Значение
     * @return $this
     */
    public function filterLt($field, $value) {
        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }
        $value = $this->db->escape($value);
        $this->filter("$field < '$value'");
        return $this;
    }

    /**
     * Фильтр "поле >= значение"
     *
     * @param string $field Имя поля
     * @param mixed $value Значение
     * @return $this
     */
    public function filterGtEqual($field, $value) {
        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }
        $value = $this->db->escape($value);
        $this->filter("$field >= '$value'");
        return $this;
    }

    /**
     * Фильтр "поле <= значение"
     *
     * @param string $field Имя поля
     * @param mixed $value Значение
     * @return $this
     */
    public function filterLtEqual($field, $value) {
        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }
        $value = $this->db->escape($value);
        $this->filter("$field <= '$value'");
        return $this;
    }

    public function filterLike($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field LIKE '$value'");
        return $this;
    }

    public function filterNotLike($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field NOT LIKE '$value'");
        return $this;
    }

    public function filterBetween($field, $start, $end){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $start = $this->db->escape($start);
        $end = $this->db->escape($end);
        $this->filter("$field BETWEEN '$start' AND '$end'");
        return $this;
    }

    public function filterDateYounger($field, $value, $interval='DAY'){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $interval = $this->db->escape($interval);
        $this->filter("$field >= DATE_SUB(NOW(), INTERVAL {$value} {$interval})");
        return $this;
    }

    public function filterTimestampYounger($field, $value, $interval='DAY'){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = (int)$value;
        $interval = $this->db->escape($interval);
        $this->filter("TIMESTAMPDIFF({$interval}, {$field}, NOW()) <= {$value}");
        return $this;
    }

    public function filterDateOlder($field, $value, $interval='DAY'){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $interval = $this->db->escape($interval);
        $this->filter("$field < DATE_SUB(NOW(), INTERVAL {$value} {$interval})");
        return $this;
    }

    public function filterTimestampGt($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $field = "UNIX_TIMESTAMP({$field})";
        $value = $this->db->escape($value);
        $this->filter("{$field} > '{$value}'");
        return $this;
    }

    public function filterTimestampLt($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $field = "UNIX_TIMESTAMP({$field})";
        $value = $this->db->escape($value);
        $this->filter("{$field} < '{$value}'");
        return $this;
    }

    public function filterIn($field, $value) {

        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }

        if (is_array($value)) {

            $values = [];

            foreach ($value as $v) {
                if (!is_array($v)) {
                    $v = $this->db->escape(strval($v));
                    $values[] = "'{$v}'";
                }
            }

            if (!$values) {
                return $this->filter('1 = 0');
            }

            $value = implode(',', $values);

        } else {

            $value = $this->db->escape($value);
        }

        return $this->filter("{$field} IN ({$value})");
    }

    public function filterNotIn($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_array($value)){
            if(!$value){ return $this; }
            foreach($value as $k=>$v){
                $v = $this->db->escape($v);
                $value[$k] = "'{$v}'";
            }
            $value = implode(',', $value);
        } else {
            $value = $this->db->escape($value);
            $value = "'{$value}'";
        }
        $this->filter("{$field} NOT IN ({$value})");
        return $this;
    }

    /**
     * Фильтр по релевантности, используя fulltext search
     * В таблице должен быть полнотекстовый индекс на $field
     *
     * @param string|array $field Имя ячейки таблицы или массив ячеек
     * @param string $value Значение, к которому нужно найти релевантные записи
     * @param string $lang Язык, используемый для стопслов
     * @return $this
     */
    public function filterRelated($field, $value, $lang = false) {

        if(!is_array($field)){
            $field = [$field];
        }

        // Реально передали не более 3х символов
        if (mb_strlen($value) <= 3) {
            return $this->filterLike($field[0], $value . '%');
        }

        $value = trim(strip_tags(mb_strtolower($value)));
        $value = trim(preg_replace('/[' . preg_quote(implode('', $this->special_chars)) . ']+/', ' ', $value));

        // После очистки осталось не более 3х символов
        // MySQL не умеет искать в полнотекстовогом индексе по 3м и менее символам
        if (mb_strlen($value) <= 3) {
            return $this->filterLike($field[0], $value . '%');
        }

        $query = [];

        $words = preg_split('/[\s,]+/', $value);

        $stopwords = string_get_stopwords($lang ? $lang : cmsConfig::get('language'));

        foreach ($words as $word) {

            if (mb_strlen($word) < 3 || is_numeric($word)) {
                continue;
            }

            if ($stopwords && in_array($word, $stopwords, true)) {
                continue;
            }
            if (mb_strlen($word) === 3) {
                $query[] = $word;
                continue;
            }

            if (mb_strlen($word) >= 12) {
                $word = mb_substr($word, 0, mb_strlen($word) - 3);
            } else if (mb_strlen($word) >= 10) {
                $word = mb_substr($word, 0, mb_strlen($word) - 2);
            } else if (mb_strlen($word) >= 6) {
                $word = mb_substr($word, 0, mb_strlen($word) - 1);
            }

            $query[] = $word . '*';
        }

        if (!$query) {

            $ft_query = '\"' . $this->db->escape($value) . '\"';
        } else {

            usort($query, function ($a, $b) {
                return mb_strlen($b) - mb_strlen($a);
            });
            $query = array_slice($query, 0, 5);

            $ft_query = '>\"' . $this->db->escape($value) . '\" <(';
            $ft_query .= implode(' ', $this->db->escape($query)) . ')';
        }

        if (strpos($field[0], '.') === false) {
            $match_fields_str = 'i.' . implode(', i.', $field);
        } else {
            $match_fields_str = implode(', ', $field);
        }

        $search_param = "MATCH({$match_fields_str}) AGAINST ('{$ft_query}' IN BOOLEAN MODE)";

        $this->select($search_param, 'fsort');

        $this->order_by = 'fsort desc';

        return $this->filter($search_param);
    }

    public function filterCategory($ctype_name, $category, $is_recursive = false, $is_multi_cats = false) {

        $table_name = $this->getContentCategoryTableName($ctype_name);
        $bind_table_name = $table_name . '_bind';

        if (!$is_recursive) {

            $this->joinInner($bind_table_name, 'b', 'b.item_id = i.id')->filterEqual('b.category_id', $category['id']);
        } else {

            // для корневой категории фильтрация не нужна
            if (!$category['parent_id']) {
                return $this;
            }

            if($is_multi_cats){
                $this->distinctSelect();
            }

            $this->joinInner($bind_table_name, 'b', 'b.item_id = i.id');
            $this->joinInner($table_name, 'c', 'c.id = b.category_id');
            $this->filterGtEqual('c.ns_left', $category['ns_left']);
            $this->filterLtEqual('c.ns_right', $category['ns_right']);
        }

        return $this;
    }

    public function filterCategoryId($ctype_name, $category_id, $is_recursive = false) {

        if (!$is_recursive) {

            if ($category_id) {
                return $this->filterCategory($ctype_name, ['id' => $category_id]);
            }
        } else {

            $category = $this->getCategory($ctype_name, $category_id);
            if ($category) {
                return $this->filterCategory($ctype_name, $category, true, true);
            }
        }

        return $this;
    }

    public function disablePrivacyFilter(){
        $this->privacy_filter_disabled = true;
        $this->privacy_filter_value = 0;
        return $this;
    }

    public function disablePrivacyFilterForFriends(){
        $this->privacy_filter_value = array(0, 1);
        return $this;
    }

    public function enablePrivacyFilter(){
        $this->privacy_filter_disabled = false;
        $this->privacy_filter_value = 0;
        return $this;
    }

    public function isEnablePrivacyFilter(){
        return $this->privacy_filter_disabled === false;
    }

    public function filterPrivacy(){

        if ($this->privacy_filtered) { return $this; }

        // Этот фильтр может применяться при подсчете числа записей
        // и при выборке самих записей
        // используем флаг чтобы фильтр не применился дважды
        $this->privacy_filtered = true;

        if(is_array($this->privacy_filter_value)){
            return $this->filterIn('i.is_private', $this->privacy_filter_value);
        }

        return $this->filterEqual('i.is_private', $this->privacy_filter_value);

    }

    public function enableDeleteFilter(){
        $this->delete_filter_disabled = false;
        return $this;
    }

    public function disableDeleteFilter(){
        $this->delete_filter_disabled = true;
        return $this;
    }

    public function enableApprovedFilter(){
        $this->approved_filter_disabled = false;
        return $this;
    }

    public function disableApprovedFilter(){
        $this->approved_filter_disabled = true;
        return $this;
    }

    public function enableHiddenParentsFilter(){
        $this->hidden_parents_filter_disabled = false;
        return $this;
    }

    public function disableHiddenParentsFilter(){
        $this->hidden_parents_filter_disabled = true;
        return $this;
    }

    public function isEnableHiddenParentsFilter(){
        return $this->hidden_parents_filter_disabled === false;
    }

    public function joinModerationsTasks($ctype_name){
        $this->select('IF(t.id IS NULL AND i.is_approved < 1, 1, NULL)', 'is_draft');
        $this->select('t.is_new_item');
        return $this->joinLeft('moderators_tasks', 't', "t.item_id = i.id AND t.ctype_name = '{$ctype_name}'");
    }

    public function filterByModeratorTask($moderator_id, $ctype_name, $is_admin = false){

        $this->select('m.is_new_item');

        $this->joinInner('moderators_tasks', 'm', 'm.item_id = i.id');

        $this->filterEqual('m.ctype_name', $ctype_name);

        if(!$is_admin){
            $this->filterEqual('m.moderator_id', $moderator_id);
        }

        return $this;

    }

    public function filterAvailableOnly(){

        if ($this->available_filtered) { return $this; }

        $this->available_filtered = true;

        return $this->filterIsNull('is_deleted');

    }

    public function filterDeleteOnly(){

        return $this->filterEqual('is_deleted', 1);

    }

    public function filterApprovedOnly(){

        if ($this->approved_filtered) { return $this; }

        // Этот фильтр может применяться при подсчете числа записей
        // и при выборке самих записей
        // используем флаг чтобы фильтр не применился дважды
        $this->approved_filtered = true;

        return $this->filterEqual('is_approved', 1);

    }

    public function filterHiddenParents(){

        if ($this->hp_filtered) { return $this; }

        $this->hp_filtered = true;

        return $this->filterIsNull('is_parent_hidden');

    }

    public function filterSubscribe($user_id){
        return $this->filterFriends($user_id, 0);
    }

    public function filterFriendsAndSubscribe($user_id){
        return $this->filterFriends($user_id, null);
    }

    public function filterFriends($user_id, $is_mutual = 1){

        $this->joinInner('{users}_friends', 'fr', 'fr.friend_id = i.user_id');

        $this->filterEqual('fr.user_id', (int)$user_id);

        if($is_mutual !== null){
            $this->filterEqual('fr.is_mutual', $is_mutual);
        } else {
            // подписчики (null) и друзья (1)
            $this->filterStart();
                $this->filterEqual('fr.is_mutual', 1);
                    $this->filterOr();
                $this->filterIsNull('fr.is_mutual');
            $this->filterEnd();
        }

        return $this;

    }

    public function filterFriendsPrivateOnly($user_id){

        // фильтр приватности при этом не нужен
        $this->privacy_filtered = true;

        return $this->filterEqual('i.is_private', 1)->filterFriends($user_id);

    }

    public function filterOnlineUsers() {
        return $this->filterNotNull('online.user_id')->filterTimestampYounger('online.date_created', cmsUser::USER_ONLINE_INTERVAL, 'SECOND');
    }

    /**
     * Применяет набор фильтров из массива
     *
     * @param array $dataset Массив фильтров/сортировки
     * @param boolean $only_filters Применять только фильтры
     * @param array $allowed_fields Разрешённые поля для фильтрации
     * @param string $table_name Имя таблицы: если передано, все поля проверяются на доступность в ней
     * @return boolean true, если что-либо применилось, false, если ничего
     */
    public function applyDatasetFilters($dataset, $only_filters = false, $allowed_fields = [], $table_name = '') {

        $success = false;

        if (!empty($dataset['filters']) && is_array($dataset['filters'])) {

            foreach ($dataset['filters'] as $filter) {

                // Небольшая валидация
                if (
                    empty($filter['field']) || !is_string($filter['field']) ||
                    empty($filter['condition'] || !is_string($filter['condition'])) ||
                    !array_key_exists('value', $filter) || (is_array($filter['value']) && $filter['condition'] !== 'in')
                    ) {
                    continue;
                }

                // Есть ли такое поле в таблице
                if ($table_name && !$this->db->isFieldExists($table_name, $filter['field'])) {
                    continue;
                }

                // Если заданы разрешенные поля, проверяем
                // валидация
                if ($allowed_fields && !in_array($filter['field'], $allowed_fields, true)) {
                    continue;
                }

                // Таблица передаётся, когда field в $dataset могут быть переданы любые
                if (!$table_name && isset($filter['callback']) && is_callable($filter['callback'])) {
                    $filter['callback']($this, $dataset);
                    continue;
                }

                if (($filter['value'] === '') && !in_array($filter['condition'], ['nn', 'ni'])) {
                    continue;
                }

                if ($filter['value'] !== '' && !is_array($filter['value'])) {
                    $filter['value'] = string_replace_user_properties($filter['value']);
                }

                $success = true;

                switch ($filter['condition']) {

                    // общие условия
                    case 'eq': $this->filterEqual($filter['field'], $filter['value']);
                        break;
                    case 'gt': $this->filterGt($filter['field'], $filter['value']);
                        break;
                    case 'lt': $this->filterLt($filter['field'], $filter['value']);
                        break;
                    case 'ge': $this->filterGtEqual($filter['field'], $filter['value']);
                        break;
                    case 'le': $this->filterLtEqual($filter['field'], $filter['value']);
                        break;
                    case 'nn': $this->filterNotNull($filter['field']);
                        break;
                    case 'ni': $this->filterIsNull($filter['field']);
                        break;

                    // строки
                    case 'lk': $this->filterLike($filter['field'], '%' . $filter['value'] . '%');
                        break;
                    case 'ln': $this->filterNotLike($filter['field'], '%' . $filter['value'] . '%');
                        break;
                    case 'lb': $this->filterLike($filter['field'], $filter['value'] . '%');
                        break;
                    case 'lf': $this->filterLike($filter['field'], '%' . $filter['value']);
                        break;

                    // даты
                    case 'dy': $this->filterDateYounger($filter['field'], $filter['value']);
                        break;
                    case 'do': $this->filterDateOlder($filter['field'], $filter['value']);
                        break;

                    // массив
                    case 'in':
                        if (!is_array($filter['value'])) {
                            $filter['value'] = explode(',', $filter['value']);
                        }
                        $this->filterIn($filter['field'], $filter['value']);
                        break;
                }
            }
        }

        if (!empty($dataset['sorting']) && !$only_filters) {

            $success = true;

            $this->orderByList($dataset['sorting']);
        }

        if (!empty($dataset['index']) && !$only_filters) {

            $success = true;

            $this->forceIndex($dataset['index'], 2);
        }

        return $success;
    }

    /**
     * Выборка по списку полей
     *
     * @param array $fields Массив полей
     * @param boolean $is_this_only Выборка только перечисленных
     * @param boolean|string $translated_table Выборка с учётом мультиязычности
     * @return $this
     */
    public function selectList($fields, $is_this_only = false, $translated_table = false) {

        if ($is_this_only) {
            $this->select = [];
        }

        foreach ($fields as $field => $alias) {

            if(is_numeric($field)){
                $field = $alias;
                $alias = false;
            }

            if (strpos($field, '.') === false){ $field = 'i.' . $field; }

            if ($translated_table) {
                $this->selectTranslatedField($field, $translated_table, $alias);
            } else {
                $this->select($field, $alias);
            }
        }

        return $this;
    }

    /**
     * Добавляет к выборке зашифрованное поле
     *
     * @param string $field Имя поля
     * @param string $as Псевдоним при выборке
     * @param mixed $key Ключ шифрования
     * @return $this
     */
    public function selectAesDecrypt($field, $as = false, $key = '') {

        $as = $as ? $as : str_replace('enc_', '', $field);

        $this->encoded_fields[] = $as ? $as : $field;

        if ($key) {
            if (is_callable($key) && ($key instanceof Closure)) {
                $key = $key($this);
            } else {
                $key = "'" . $this->db->escape($key) . "'";
            }
            $field = "AES_DECRYPT(`{$field}`, {$key})";
        } else {
            $field = "AES_DECRYPT(`{$field}`, @aeskey)";
        }

        return $this->select($field, $as);
    }

    /**
     * Добавляет поле к выборке
     *
     * @param string $field Имя поля, желательно с префиксом таблицы (например, i.title)
     * @param string $as Псевдоним при выборке
     * @return $this
     */
    public function select($field, $as = false) {

        $this->select[] = $as ? $field . ' as `' . $as . '`' : $field;

        return $this;
    }

    /**
     * Добавляет поле к выборке, пытаясь найти его
     * с постфиксом языка
     *
     * @param string $field Имя поля
     * @param string $table Таблица, откуда выбирается это поле
     * @param string $as Псевдоним при выборке
     * @return $this
     */
    public function selectTranslatedField($field, $table, $as = false) {

        if ($this->lang === $this->default_lang) {
            return $this->select($field, $as);
        }

        // Имя с переводом, с учётом префикса таблицы
        $field_name = $field . '_' . $this->lang;

        // Переведённое поле выбираем по имени оригинального
        $select_as_name = (strpos($field, '.') === false ? $field : ltrim(strrchr($field, '.'), '.'));

        // Нет поля с переводом. В isFieldExists не учитывается префикс таблицы
        if (!$this->db->isFieldExists($table, $select_as_name . '_' . $this->lang)) {
            $field_name = $field;
        }

        return $this->select($field_name, ($as ? $as : $select_as_name));
    }

    /**
     * Выбор из таблицы только указанного поля
     *
     * @param string $field Имя поля
     * @param string $as Псевдоним при выборке
     * @return $this
     */
    public function selectOnly($field, $as = false) {

        $this->select = [];

        return $this->select($field, $as);
    }

    /**
     * Возвращает имя поля, учитывая язык
     *
     * @param string $field Имя поля
     * @param string $table Таблица, в котором это поле есть
     * @return string
     */
    public function getTranslatedFieldName($field, $table = '') {

        if ($this->lang === $this->default_lang) {
            return $field;
        }

        // Имя с переводом, с учётом префикса таблицы
        $field_name = $field . '_' . $this->lang;

        if($table){

            // Убираем алиас уточнения таблицы
            $check_name = (strpos($field_name, '.') === false ? $field_name : ltrim(strrchr($field_name, '.'), '.'));

            // Нет поля с переводом
            if (!$this->db->isFieldExists($table, $check_name)) {
                $field_name = $field;
            }
        }

        return $field_name;
    }

    /**
     * Проверяет, присоединена ли была таблица с таким псевдонимом
     *
     * @param string $table_name Имя таблицы
     * @param string $as         Краткий псевдоним
     * @return boolean
     */
    public function isJoined($table_name, $as) {
        return strpos($this->join, '{#}' . $table_name . ' as ' . $as) !== false;
    }

    /**
     * Присоединяет результат подзапроса
     *
     * @param string $query     Подзапрос
     * @param string $as        Краткий псевдоним
     * @param string $on        Условия присоединения
     * @param string $join_type Тип присоединения
     * @return \cmsModel
     */
    public function joinQuery($query, $as, $on, $join_type = self::INNER_JOIN) {
        $this->join .= $join_type . ' ' . $query . ' as ' . $as . ' ON ' . $on . PHP_EOL;
        return $this;
    }

    /**
     * Простое присоединение (псевдоним joinInner)
     *
     * @param string $table_name Имя таблицы
     * @param string $as         Краткий псевдоним
     * @param string $on         Условие связи по полям
     * @return \cmsModel
     */
    public function join($table_name, $as, $on) {
        return $this->joinInner($table_name, $as, $on);
    }

    /**
     * Простое присоединение (затмение)
     * (данные, которые есть в основной и присоединяемой таблицах)
     *
     * @param string $table_name Имя таблицы
     * @param string $as         Краткий псевдоним
     * @param string $on         Условие связи по полям
     * @return \cmsModel
     */
    public function joinInner($table_name, $as, $on) {
        $this->join .= self::INNER_JOIN . ' {#}' . $table_name . ' as ' . $as . ' ON ' . $on . PHP_EOL;
        return $this;
    }

    /**
     * Левостороннее присоединение (полнолуние)
     *
     * @param string $table_name Имя таблицы
     * @param string $as         Краткий псевдоним
     * @param string $on         Условие связи по полям
     * @return \cmsModel
     */
    public function joinLeft($table_name, $as, $on) {
        $this->join .= self::LEFT_JOIN . ' {#}' . $table_name . ' as ' . $as . ' ON ' . $on . PHP_EOL;
        return $this;
    }

    /**
     * Левостороннее присоединение, исключая данные справа, которые есть по связи
     * (полумесяц слева)
     *
     * @param string $table_name  Имя таблицы
     * @param string $as          Краткий псевдоним
     * @param string $right_key   Имя поля связи присоединяемой таблицы
     * @param string $left_key    Имя поля связи основной таблицы
     * @param string $join_where  Дополнительные условия присоединения
     * @return \cmsModel
     */
    public function joinExcludingLeft($table_name, $as, $right_key, $left_key, $join_where = '') {
        $this->join .= self::LEFT_JOIN . ' {#}' . $table_name . ' as ' . $as .
                ' ON ' . $left_key . '=' . $right_key . ($join_where ? ' AND ' . $join_where : '') . PHP_EOL;
        $this->filter($right_key . ' IS NULL');
        return $this;
    }

    /**
     * Правостороннее присоединение (полнолуние в отражении)
     * Данные будут браться из этой таблицы и сравниваться с основной
     *
     * @param string $table_name Имя таблицы
     * @param string $as         Краткий псевдоним
     * @param string $on         Условие связи по полям
     * @return \cmsModel
     */
    public function joinRight($table_name, $as, $on) {
        $this->join .= self::RIGHT_JOIN . ' {#}' . $table_name . ' as ' . $as . ' ON ' . $on . PHP_EOL;
        return $this;
    }

    /**
     * Правостороннее присоединение, исключая данные слева, которые есть по связи
     * (полумесяц справа)
     *
     * @param string $table_name  Имя таблицы
     * @param string $as          Краткий псевдоним
     * @param string $right_key   Имя поля связи присоединяемой таблицы
     * @param string $left_key    Имя поля связи основной таблицы
     * @param string $join_where  Дополнительные условия присоединения
     * @return \cmsModel
     */
    public function joinExcludingRight($table_name, $as, $right_key, $left_key, $join_where = '') {
        $this->join .= self::RIGHT_JOIN . ' {#}' . $table_name . ' as ' . $as .
                ' ON ' . $left_key . '=' . $right_key . ($join_where ? ' AND ' . $join_where : '') . PHP_EOL;
        $this->filter($left_key . ' IS NULL');
        return $this;
    }

    /**
     * Присоединяет к выборке таблицу пользователей
     *
     * @param string $on_field        Имя поля основной таблицы, содержащее id пользователя
     * @param array $user_fields      Поля, необходимые для выборки из таблицы пользователей
     * @param boolean $join_direction Как присоединять таблицу: left|right|inner
     * @param string $as              Псевдоним присоединяемой таблицы
     * @return \cmsModel
     */
    public function joinUser($on_field = 'user_id', $user_fields = [], $join_direction = false, $as = 'u') {

        if (!$user_fields) {
            $user_fields = [
                $as . '.nickname'        => 'user_nickname',
                $as . '.slug'            => 'user_slug',
                $as . '.is_deleted'      => 'user_is_deleted',
                $as . '.groups'          => 'user_groups',
                $as . '.avatar'          => 'user_avatar',
                $as . '.privacy_options' => 'user_privacy_options'
            ];
        }

        foreach ($user_fields as $field => $alias) {
            $this->select($field, $alias);
        }

        switch ($join_direction) {

            case 'left':
                $this->joinLeft('{users}', $as, $as . '.id = i.' . $on_field);
                break;

            case 'right':
                $this->joinRight('{users}', $as, $as . '.id = i.' . $on_field);
                break;

            default:
                $this->joinInner('{users}', $as, $as . '.id = i.' . $on_field);
                break;
        }

        return $this;
    }

    /**
     * Присоединяет к выборке таблицу пользователей слева
     *
     * @param string $on_field   Имя поля основной таблицы, содержащее id пользователя
     * @param array $user_fields Поля, необходимые для выборки из таблицы пользователей
     * @return \cmsModel
     */
    public function joinUserLeft($on_field = 'user_id', $user_fields = []) {
        return $this->joinUser($on_field, $user_fields, 'left');
    }

    /**
     * Присоединяет к выборке таблицу пользователей справа
     *
     * @param string $on_field   Имя поля основной таблицы, содержащее id пользователя
     * @param array $user_fields Поля, необходимые для выборки из таблицы пользователей
     * @return \cmsModel
     */
    public function joinUserRight($on_field = 'user_id', $user_fields = []) {
        return $this->joinUser($on_field, $user_fields, 'right');
    }

    /**
     * Присоединяет таблицу онлайн пользователей
     *
     * @param string $as Псевдоним таблицы пользователей
     * @return \cmsModel
     */
    public function joinSessionsOnline($as = 'u') {

        if (!empty($this->joined_session_online[$as])) {
            return $this;
        }

        $this->joinLeft('sessions_online', 'online', 'online.user_id = ' . $as . '.id');
        $this->select('IF(online.date_created IS NOT NULL AND TIMESTAMPDIFF(SECOND, online.date_created, NOW()) <= ' . cmsUser::USER_ONLINE_INTERVAL . ', 1, 0)', 'is_online');

        $this->joined_session_online[$as] = true;

        return $this;
    }

    /**
     * Устанавливает группировку запроса
     *
     * @param array|string $field Имя поля или массив полей
     * @return $this
     */
    public function groupBy($field) {

        $group_by = [];

        if (!is_array($field)) {
            $field = [$field];
        }

        foreach ($field as $field_name) {

            if (strpos($field_name, '.') === false) {
                $field_name = 'i.' . $field_name;
            }

            $group_by[] = $field_name;
        }

        $this->group_by = implode(', ', $group_by);

        return $this;
    }

    /**
     * Добавляет необработанное HAVING к запросу с группировкой
     *
     * @param string $condition Выражение
     * @return $this
     */
    public function havingRaw($condition) {

        if (!$this->group_by) {
            return $this;
        }

        $this->group_by .= PHP_EOL . 'HAVING ' . $condition;

        return $this;
    }

    /**
     * Добавляет HAVING к запросу с группировкой
     *
     * @param string $field Имя поля
     * @param string $operator Оператор сравнения
     * @param mixed $value Значение
     * @return $this
     */
    public function having($field, $operator, $value) {

        $value = $this->db->escape($value);

        return $this->havingRaw("{$field} {$operator} '{$value}'");
    }

    /**
     * Управляет индексами в запросе
     * @param string $index_name Название индекса в БД
     * @param string $action FORCE | IGNORE | USE
     * @param int $for 1 - FOR JOIN, 2 - FOR ORDER BY, 3 - FOR GROUP BY
     * @return \cmsModel
     */
    protected function indexHint($index_name, $action, $for='') {
        switch ($for) {
            case 1:
                $for_action = 'FOR JOIN';
                break;
            case 2:
                $for_action = 'FOR ORDER BY';
                break;
            case 3:
                $for_action = 'FOR GROUP BY';
                break;
            default:
                $for_action = '';
                break;
        }
        $this->index_action .= "{$action} INDEX {$for_action} ({$index_name})\n";
        return $this;
    }

    public function forceIndex($index_name, $for='') {
        return $this->indexHint($index_name, 'FORCE', $for);
    }

    public function ignoreIndex($index_name, $for='') {
        return $this->indexHint($index_name, 'IGNORE', $for);
    }

    public function useIndex($index_name, $for='') {
        return $this->indexHint($index_name, 'USE', $for);
    }

    /**
     * Сортировка по полю, у которого может быть перевод
     * Если поле с переводом есть, отсортируется по нему
     *
     * @param string $field Название ячейки БД без языкового префикса
     * @param string $direction Направление сортировки
     * @param string $table Таблица, где находится ячейка. Не указана - проверки не будет
     * @return $this
     */
    public function orderByTranslatedField($field, $direction = 'asc', $table = '') {

        return $this->orderBy($this->getTranslatedFieldName($field, $table), $direction);
    }

    /**
     * Устанавливает сортировку
     *
     * @param string $order_by
     * @return $this
     */
    public function orderByRaw($order_by) {

        $this->order_by = $order_by;

        return $this;
    }

    /**
     * Устанавливает сортировку
     *
     * @param string $field Поле для сортировки
     * @param string $direction Направление сортировки
     * @param boolean $is_force_index_by_field deprecated
     * @return $this
     */
    public function orderBy($field, $direction = '', $is_force_index_by_field = false) {

        if (strpos($field, '(') !== false) {
            return $this;
        } // в названии поля не может быть функции
        if ($direction) {
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        }
        if (strpos($field, '.') === false) {
            $field = 'i.' . $field;
        }

        return $this->orderByRaw($field . ' ' . $direction);
    }

    /**
     * Устанавливает множественную сортировку
     *
     * @param array $list Массив сортировок с ключами by и to
     * @return $this
     */
    public function orderByList($list) {

        $this->order_by = '';

        if (is_array($list)) {

            foreach ($list as $o) {

                if (strpos($o['by'], '(') !== false) {
                    continue;
                }

                $field     = $o['by'];
                $direction = strtolower($o['to']) === 'desc' ? 'desc' : 'asc';

                if (empty($o['strict']) && strpos($field, '.') === false) {
                    $field = 'i.' . $field;
                }
                if ($this->order_by) {
                    $this->order_by .= ', ';
                }
                $this->order_by .= $field . ' ' . $direction;
            }
        }

        return $this;
    }

    public function limit($from, $howmany = 0) {

        $this->limit = (int) $from;
        $howmany     = (int) $howmany;

        if ($this->limit < 0) {
            $this->limit = 0;
        }

        if ($howmany) {
            if ($howmany <= 0) {
                $howmany = 15;
            }
            $this->limit .= ', ' . $howmany;
        }

        return $this;
    }

    public function limitPage($page, $perpage = 0) {

        $page    = (int) $page;
        $perpage = (int) $perpage;

        if ($perpage <= 0) {
            $perpage = $this->perpage;
        }

        return $this->limit(($page - 1) * $perpage, $perpage);
    }

    public function limitPagePlus($page, $perpage = 0) {

        $page    = (int) $page;
        $perpage = (int) $perpage;

        if ($perpage <= 0) {
            $perpage = $this->perpage;
        }

        return $this->limit(($page - 1) * $perpage, $perpage + 1);
    }

    public function setPerPage($perpage) {

        $this->perpage = (int) $perpage;

        return $this;
    }

    public function setReadType($type) {

        $this->read_type = $type;

        return $this;
    }

//============================================================================//
//============================================================================//

    public function getField($table_name, $row_id, $field_name) {

        return $this->filterEqual('id', $row_id)->
                getFieldFiltered($table_name, $field_name);
    }

    public function getFieldFiltered($table_name, $field_name) {

        $this->selectOnly($field_name);

        $item = $this->getItem($table_name);

        if (!$item) {
            return false;
        }

        return $item[$field_name];
    }

//============================================================================//
//============================================================================//

    public function getItem($table_name, $item_callback = false) {

        $this->table = $table_name;

        $this->limit(1);

        $sql = $this->getSQL();

        $encoded_fields = $this->encoded_fields;

        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key) {

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache     = cmsCache::getInstance();

            $item = $cache->get($cache_key);

            if ($item) {

                if (is_callable($item_callback)) {
                    $item = call_user_func_array($item_callback, [$item, $this]);
                }

                if ($this->localized) {
                    $item = $this->replaceTranslatedField($item, $table_name);
                }

                $this->stopCache();

                return $item;
            }
        }

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)) {
            return false;
        }

        $item = $this->db->fetchAssoc($result);

        // для кеша формируем массив без обработки коллбэком
        if ($this->cache_key) {
            $_item = $item;
        }

        if ($encoded_fields) {
            foreach ($encoded_fields as $field) {
                $item[$field] = base64_decode($item[$field]);
                unset($item['enc_' . $field]);
            }
        }

        if (is_callable($item_callback)) {
            $item = call_user_func_array($item_callback, [$item, $this]);
        }

        if ($this->localized) {
            $item = $this->replaceTranslatedField($item, $table_name);
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key) {
            $cache->set($cache_key, $_item);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        return $item;
    }

    public function getItemById($table_name, $id, $item_callback = false) {

        return $this->getItemByField($table_name, 'id', $id, $item_callback);
    }

    public function getItemByField($table_name, $field_name, $field_value, $item_callback = false) {

        return $this->filterEqual($field_name, $field_value)->
                getItem($table_name, $item_callback);
    }

//============================================================================//
//============================================================================//
    /**
     * Возвращает количество записей по условиям
     *
     * @param string $table_name Имя таблицы
     * @param string $by_field Поле подсчёта
     * @param boolean $reset Флаг сброса условий фильтрации
     * @return integer
     */
    public function getCount($table_name, $by_field = 'id', $reset = false) {

        if(!$by_field){
            $select = "{$this->distinct} 1";
        } else {
            $select = "COUNT({$this->distinct} i.{$by_field} ) as `count`";
        }

        $sql = "SELECT {$this->straight_join} {$select}
                FROM {#}{$table_name} i
                {$this->index_action}";

        if ($this->join) { $sql .= $this->join; }

        if ($this->where) { $sql .= 'WHERE ' . $this->where . PHP_EOL; }

        if ($this->group_by) { $sql .= 'GROUP BY ' . $this->group_by . PHP_EOL; }

        if ($reset) {
            $this->resetFilters();
        }

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key) {

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($result = $cache->get($cache_key))) {
                $this->stopCache();
                return $result;
            }
        }

        $result = $this->db->query($sql);

        $num_rows = $this->db->numRows($result);

        if(!$by_field){

            $count = $num_rows;

        } else {

            if (!$num_rows) {
                $count = 0;
            } else {
                $item  = $this->db->fetchAssoc($result);
                $count = intval($item['count']);
            }
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key) {
            $cache->set($cache_key, $count);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        return $count;
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает записи из базы, применяя все наложенные ранее фильтры
     *
     * @param string $table_name Имя таблицы
     * @param callable $item_callback Коллбэк функция
     * @param string $key_field Имя ячейки массива из БД, значение которой станет ключём в результирующем массиве
     * @return array
     */
    public function get($table_name, $item_callback = false, $key_field = 'id') {

        $this->table = $table_name;

        $items = $_items = [];

        $sql = $this->getSQL();

        $encoded_fields = $this->encoded_fields;

        // сбрасываем фильтры
        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key) {

            $cache_key = $this->cache_key . '.' . md5($sql);

            $cache = cmsCache::getInstance();

            $_items = $cache->get($cache_key);

            if ($_items !== false) {

                $this->stopCache();

                // обрабатываем коллбэком
                if (is_callable($item_callback)) {

                    foreach ($_items as $key => $item) {

                        $item = call_user_func_array($item_callback, [$item, $this]);
                        if ($item === false) {
                            continue;
                        }

                        if ($this->localized) {
                            $item = $this->replaceTranslatedField($item, $table_name);
                        }

                        $items[$key] = $item;
                    }
                } else {
                    return $_items;
                }

                return $items;
            } else {
                $_items = [];
            }
        }

        $result = $this->db->query($sql);

        // если запрос ничего не вернул, возвращаем ложь
        if (!$this->db->numRows($result)) {
            return false;
        }

        // перебираем все вернувшиеся строки
        while ($item = $this->db->fetchAssoc($result)) {

            $key = ($key_field && isset($item[$key_field])) ? $item[$key_field] : false;

            // для кеша формируем массив без обработки коллбэком
            if ($this->cache_key) {
                if ($key) {
                    $_items[$key] = $item;
                } else {
                    $_items[] = $item;
                }
            }

            if ($encoded_fields) {
                foreach ($encoded_fields as $efield) {
                    $item[$efield] = base64_decode($item[$efield]);
                    unset($item['enc_' . $efield]);
                }
            }

            // если задан коллбек для обработки строк,
            // то пропускаем строку через него
            if (is_callable($item_callback)) {
                $item = call_user_func_array($item_callback, [$item, $this]);
                if ($item === false) {
                    continue;
                }
            }

            if ($this->localized) {
                $item = $this->replaceTranslatedField($item, $table_name);
            }

            // добавляем обработанную строку в результирующий массив
            if ($key) {
                $items[$key] = $item;
            } else {
                $items[] = $item;
            }
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        // сохраняем не обработанный коллбэком массив
        if ($this->cache_key) {
            $cache->set($cache_key, $_items);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        // возвращаем строки
        return $items;
    }

//============================================================================//
//============================================================================//

    public function getSQL() {

        $select = implode(', ', $this->select);

        $sql = "SELECT {$this->distinct} {$this->straight_join} {$select}
                FROM {#}{$this->table} i
                {$this->index_action}";

        if ($this->join) {
            $sql .= $this->join;
        }

        if ($this->where) {
            $sql .= 'WHERE ' . $this->where . PHP_EOL;
        }

        if ($this->group_by) {
            $sql .= 'GROUP BY ' . $this->group_by . PHP_EOL;
        }

        if ($this->order_by) {
            $sql .= 'ORDER BY ' . $this->order_by . PHP_EOL;
        }

        if ($this->limit) {
            $sql .= 'LIMIT ' . $this->limit . PHP_EOL;
        }

        if ($this->read_type) {
            $sql .= $this->read_type . PHP_EOL;
        }

        return $sql;
    }

//============================================================================//
//============================================================================//
    /**
     * Возвращает максимальное или минимальное
     * значение поля таблицы
     *
     * @param string $table Таблица
     * @param string $field Название поля
     * @param integer $default Значение по умолчанию
     * @param string $dir Направление: MAX или MIN
     * @return integer
     */
    public function getMax($table, $field, $default = 0, $dir = 'MAX') {

        $sql = "SELECT {$dir}(i.{$field}) as {$field}
                FROM {#}{$table} i
                ";

        if ($this->where) {
            $sql .= 'WHERE ' . $this->where . PHP_EOL;
        }

        $sql .= 'LIMIT 1';

        $result = $this->db->query($sql);

        $this->resetFilters();

        if (!$this->db->numRows($result)) {
            return $default;
        }

        $max = $this->db->fetchAssoc($result);

        $this->db->freeResult($result);

        return $max[$field] ?: 0;
    }

    public function getMin($table, $field, $default = 0) {
        return $this->getMax($table, $field, $default, 'MIN');
    }

    /**
     * Возвращает максимальный порядковый номер в таблице
     * @param string $table
     * @param string $where
     * @return int
     */
    public function getMaxOrdering($table){

        return $this->getMax($table, 'ordering');

    }

    /**
     * Возращает следующий порядковый номер в таблице для новых записей
     * @param string $table
     * @param string $where
     * @return int
     */
    public function getNextOrdering($table){

        return $this->getMaxOrdering($table) + 1;

    }

    /**
     * Пересчитывает порядковые номера в таблице
     *
     * @param string $table_name Таблица БД
     * @return boolean
     */
    public function reorder($table_name) {

        $list = $this->limit(false)->
            orderBy('ordering', 'asc')->selectOnly('id')->
            get($table_name, function ($item, $model) {
            return $item['id'];
        }, false) ?: [];

        return $this->reorderByList($table_name, $list);
    }

    /**
     * Расставляет порядковые номера для списка из ID записей
     *
     * @param string $table_name Таблица БД
     * @param array $list Массив id записей в нужном порядке
     * @param array $additional_fields Список дополнительных полей и их значений, которые нужно обновлять вместе с ordering
     * @param string $field_name Название поля
     * @return boolean
     */
    public function reorderByList($table_name, $list, $additional_fields = [], $field_name = 'ordering') {

        $ordering = 0;

        foreach ($list as $id) {

            if (is_array($id) || !is_numeric($id)) {
                continue;
            }

            $ordering += 1;

            $this->update($table_name, $id, array_merge($additional_fields, [$field_name => $ordering]));
        }

        return $ordering > 0 ? true : false;
    }

//============================================================================//
//============================================================================//

    /**
     * Применяет к модели фильтры, переданные из просмотра
     * таблицы со списком записей
     * Метод совместимости, не используйте его
     *
     * @param cmsGrid $grid Объект грида
     * @param array $filter
     * @param string $table_name
     * @return $this
     */
    public function applyGridFilter(cmsGrid $grid, $filter, $table_name = '') {

        $grid->applyGridFilter($this, $filter, $table_name);

        return $this;
    }

//============================================================================//
//============================================================================//

    /**
     * Изменяет числовое поле в таблице на величину $step
     *
     * @param string $table Имя таблицы
     * @param string $field Имя поля
     * @param type $step Шаг изменения
     * @return boolean
     */
    public function increment($table, $field, $step = 1) {

        $step = (float)$step;

        $sign = $step > 0 ? '+' : '-';
        $step = abs($step);

        $sql = "UPDATE {#}{$table} i
                SET i.{$field} = i.{$field} {$sign} {$step}
                ";

        if ($this->where) {
            $sql .= 'WHERE ' . $this->where;
        }

        $this->resetFilters();

        return $this->db->query($sql, false, true) ? true : false;
    }

    /**
     * Изменяет числовое поле в таблице на величину $step с противоположенным знаком
     *
     * @param string $table Имя таблицы
     * @param string $field Имя поля
     * @param type $step Шаг изменения
     * @return boolean
     */
    public function decrement($table, $field, $step = 1) {
        return $this->increment($table, $field, $step * -1);
    }

    /**
     * Удаляет известные данные о контроллере $name в таблицах БД
     *
     * @param string $name Имя контроллера
     * @return boolean
     */
    public function deleteController($name) {

        if(is_numeric($name)){
            $controller = $this->getItemById('controllers', $name);
            $name = $controller['name'];
        }

        $this->filterEqual('controller', $name)->deleteFiltered('{users}_tabs');
        $this->filterEqual('listener', $name)->deleteFiltered('events');

        cmsCache::getInstance()->clean('events');

        $rule_ids = $this->selectOnly('id')->filterEqual('controller', $name)->get('perms_rules', function($item, $model){
            return $item['id'];
        }, false);

        if($rule_ids){
            $this->filterIn('rule_id', $rule_ids)->deleteFiltered('perms_users');
            $this->filterEqual('controller', $name)->deleteFiltered('perms_rules');
        }

        $this->filterEqual('controller', $name)->deleteFiltered('scheduler_tasks');

        return $this->filterEqual('name', $name)->deleteFiltered('controllers');
    }

    public function fieldsAfterStore($item, $fields, $action = 'add') {

        foreach ($fields as $field) {
            $field['handler']->afterStore($item, $this, $action);
        }

        return $this;
    }

    /**
     * Возвращает количество массив количества записей в черновиках
     * Для нужных контроллеров должна быть переопределена
     * в их моделях
     *
     * @param integer $user_id
     * @return integer | array
     */
    public function getDraftCounts($user_id){
        return 0;
    }

//============================================================================//
//============================================================================//

    /**
     * Сортирует элементы массива $items в виде плоского дерева
     * на основании связей через parent_id
     * @param array $items
     * @param array $result_tree
     * @param int $parent_id
     * @param int $level
     */
    public static function buildTreeRecursive($items, &$result_tree, $parent_id=0, $level=1){
        $level++;
        foreach($items as $item){
            if ($item['parent_id']==$parent_id){
                $item['level'] = $level-1;
                if (!isset($result_tree[$item['id']])){
                    $result_tree[$item['id']] = $item;
                }
                self::buildTreeRecursive($items, $result_tree, $item['id'], $level);
            }
        }
    }

//============================================================================//
//============================================================================//

    /**
     * Преобразует массив в YAML
     * @param array $input_array
     * @param integer $indent
     * @param integer $word_wrap
     * @return string
     */
    public static function arrayToYaml($input_array, $indent = 2, $word_wrap = 40) {

        $array = [];

        if (cmsConfig::get('native_yaml')) {

            if (!empty($input_array)) {
                $array = $input_array;
            }

            return yaml_emit($array, YAML_UTF8_ENCODING);
        }

        if (!empty($input_array)) {
            foreach ($input_array as $key => $value) {
                $_k = str_replace(['[', ']'], '', $key); // был фатальный баг, если в ключах эти символы
                $array[$_k] = $value;
            }
        }

        return Spyc::YAMLDump($array, $indent, $word_wrap);
    }

    /**
     * Преобразует YAML в массив
     * @param string $yaml
     * @return array
     */
    public static function yamlToArray($yaml) {

        if (!$yaml) { return []; }

        if (is_array($yaml)) { return $yaml; }

        if ($yaml === "---\n- 0\n") { return []; }
        if ($yaml === "---\n- \"0\"\n...\n") { return []; }

        if (cmsConfig::get('native_yaml')) {
            return yaml_parse($yaml);
        }

        return Spyc::YAMLLoadString($yaml);
    }

    /**
     * Преобразует массив в строку
     * @param array $input_array
     * @return string
     */
    public static function arrayToString($input_array) {
        if (!is_array($input_array)) {
            return null;
        }
        return json_encode($input_array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Преобразует строку в массив
     * @param string $string
     * @return array
     */
    public static function stringToArray($string) {
        if (!$string) { return []; }
        return (array) json_decode($string, true);
    }

    /**
     * Кеширует данные в пределах запроса
     * @param string $key Ключ
     * @param mixed $data Данные
     */
    public static function cacheResult($key, $data) {
        self::$cached[$key] = $data;
    }
    public static function getCachedResult($key) {
        if (isset(self::$cached[$key])) {
            return self::$cached[$key];
        }
        return null;
    }

}
