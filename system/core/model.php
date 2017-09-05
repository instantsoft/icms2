<?php
class cmsModel {

    public $name;

    public $db;

	const LEFT_JOIN = 'LEFT JOIN';
	const RIGHT_JOIN = 'RIGHT JOIN';
	const INNER_JOIN = 'INNER JOIN';
	const STRAIGHT_JOIN = 'STRAIGHT_JOIN';
	const LEFT_OUTER_JOIN = 'LEFT OUTER JOIN';
	const RIGHT_OUTER_JOIN = 'RIGHT OUTER JOIN';
	const NATURAL_LEFT_JOIN = 'NATURAL LEFT JOIN';
	const NATURAL_LEFT_OUTER_JOIN = 'NATURAL LEFT OUTER JOIN';
	const NATURAL_RIGHT_JOIN = 'NATURAL RIGHT JOIN';
	const NATURAL_RIGHT_OUTER_JOIN = 'NATURAL RIGHT OUTER JOIN';

    //условия для выборок
    public $table      = '';
    public $select     = array('i.*');
    public $distinct   = '';
    public $straight_join = '';
    public $join       = '';
    public $where      = '';
    public $where_separator  = 'AND';
    public $group_by   = '';
    public $order_by   = '';
    public $index_action = '';
    public $limit      = 1000;
    public $perpage    = 50;

    public $keep_filters = false;
    public $filter_on  = false;

    protected $privacy_filter_disabled = false;
    protected $privacy_filtered = false;
    protected $approved_filter_disabled = false;
    protected $delete_filter_disabled = false;
    protected $approved_filtered = false;
    protected $available_filtered = false;

    protected static $cached = array();

    private $cache_key = false;

    public function __construct(){

        $this->name = str_replace('model_', '', get_called_class());

        $this->db = cmsCore::getInstance()->db;

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

    public function getContentTableStruct(){

        return array(
            'id'                 => array('type' => 'primary'),
            'title'              => array('type' => 'varchar', 'size' => 100, 'fulltext' => true),
            'content'            => array('type' => 'text'),
            'photo'              => array('type' => 'text'),
            'slug'               => array('type' => 'varchar', 'index' => true, 'size' => 100),
            'seo_keys'           => array('type' => 'varchar', 'size' => 256),
            'seo_desc'           => array('type' => 'varchar', 'size' => 256),
            'seo_title'          => array('type' => 'varchar', 'size' => 256),
            'tags'               => array('type' => 'varchar', 'size' => 1000),
            'date_pub'           => array('type' => 'timestamp', 'index' => array('date_pub','parent_id', 'user_id'), 'composite_index' => array(4,3,2,1), 'default_current' => true),
            'date_last_modified' => array('type' => 'timestamp'),
            'date_pub_end'       => array('type' => 'timestamp', 'index' => true),
            'is_pub'             => array('type' => 'bool', 'index' => 'date_pub', 'composite_index' => 0, 'default' => 1),
            'hits_count'         => array('type' => 'int', 'default' => 0, 'unsigned' => true),
            'user_id'            => array('type' => 'int', 'index' => 'user_id', 'composite_index' => 0, 'unsigned' => true),
            'parent_id'          => array('type' => 'int', 'index' => 'parent_id', 'composite_index' => 0, 'unsigned' => true),
            'parent_type'        => array('type' => 'varchar', 'size' => 32, 'index' => 'parent_id', 'composite_index' => 1),
            'parent_title'       => array('type' => 'varchar', 'size' => 100),
            'parent_url'         => array('type' => 'varchar', 'size' => 255),
            'is_parent_hidden'   => array('type' => 'bool', 'index' => 'date_pub', 'composite_index' => 1),
            'category_id'        => array('type' => 'int', 'index' => true, 'default' => 1, 'unsigned' => true),
            'folder_id'          => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'is_comments_on'     => array('type' => 'bool', 'default' => 1),
            'comments'           => array('type' => 'int', 'default' => 0, 'unsigned' => true),
            'rating'             => array('type' => 'int', 'default' => 0),
            'is_deleted'         => array('type' => 'bool', 'index' => 'date_pub', 'composite_index' => 2),
            'is_approved'        => array('type' => 'bool', 'index' => 'date_pub', 'composite_index' => 3, 'default' => 1),
            'approved_by'        => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'date_approved'      => array('type' => 'timestamp'),
            'is_private'         => array('type' => 'bool', 'default' => 0)
        );

    }

    public function getFieldsTableStruct(){

        return array(
            'id'            => array('type' => 'primary'),
            'ctype_id'      => array('type' => 'int', 'unsigned' => true),
            'name'          => array('type' => 'varchar', 'size' => 40),
            'title'         => array('type' => 'varchar', 'size' => 100),
            'hint'          => array('type' => 'varchar', 'size' => 200),
            'ordering'      => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'fieldset'      => array('type' => 'varchar', 'size' => 32),
            'type'          => array('type' => 'varchar', 'size' => 16),
            'is_in_list'    => array('type' => 'bool'),
            'is_in_item'    => array('type' => 'bool'),
            'is_in_filter'  => array('type' => 'bool'),
            'is_private'    => array('type' => 'bool'),
            'is_fixed'      => array('type' => 'bool'),
            'is_fixed_type' => array('type' => 'bool'),
            'is_system'     => array('type' => 'bool'),
            'values'        => array('type' => 'text'),
            'options'       => array('type' => 'text'),
            'groups_read'   => array('type' => 'text'),
            'groups_edit'   => array('type' => 'text'),
            'filter_view'   => array('type' => 'text')
        );

    }

    public function getPropsTableStruct(){

        return array(
            'id'           => array('type' => 'primary'),
            'ctype_id'     => array('type' => 'int', 'unsigned' => true),
            'title'        => array('type' => 'varchar', 'size' => 100),
            'fieldset'     => array('type' => 'varchar', 'size' => 32),
            'type'         => array('type' => 'varchar', 'size' => 16),
            'is_in_filter' => array('type' => 'bool', 'index' => true),
            'values'       => array('type' => 'text'),
            'options'      => array('type' => 'text')
        );

    }

    public function getPropsBindTableStruct(){

        return array(
            'id'       => array('type' => 'primary'),
            'prop_id'  => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'cat_id'   => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'ordering' => array('type' => 'int', 'index' => true, 'unsigned' => true),
        );

    }

    public function getPropsValuesTableStruct(){

        return array(
            'prop_id' => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'item_id' => array('type' => 'int', 'index' => true, 'unsigned' => true),
            'value'   => array('type' => 'varchar', 'size' => 255),
        );

    }

//============================================================================//
//============================================================================//

    public function getRootCategory($ctype_name){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        return $this->db->getFields($table_name, 'parent_id=0');

    }

    public function getCategory($ctype_name, $id, $by_field='id'){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->useCache('content.categories');

        $category = $this->getItemByField($table_name, $by_field, $id);

        if (!$category) { return false; }

        $category['path'] = $this->getCategoryPath($ctype_name, $category);

        if(!empty($category['allow_add'])){
            $category['allow_add'] = cmsModel::yamlToArray($category['allow_add']);
        }

        return $category;

    }

    public function getCategoryBySLUG($ctype_name, $slug){

        return $this->getCategory($ctype_name, $slug, 'slug');

    }

    public function getCategorySLUG($category){

        $slug = '';

        foreach($category['path'] as $c){
            if ($c['id'] == 1) { continue; }
            if ($slug) { $slug .= '/'; }
            $slug .= lang_slug( empty($c['slug_key']) ? str_replace('/', '', $c['title']) : $c['slug_key'] );
        }

        return $slug;

    }

//============================================================================//
//============================================================================//

    public function getCategoriesTree($ctype_name, $is_show_root=true) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        if (!$is_show_root){
            $this->filterGt('parent_id', 0);
        }

        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($table_name, function($node, $model){
            if ($node['ns_level']==0) { $node['title'] = LANG_ROOT_CATEGORY; }
            if(!empty($node['allow_add'])){
                $node['allow_add'] = cmsModel::yamlToArray($node['allow_add']);
            }
            return $node;
        });

    }

//============================================================================//
//============================================================================//

    public function getSubCategories($ctype_name, $parent_id=1) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->filterEqual('parent_id', $parent_id);
        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($table_name);

    }

    public function getSubCategoriesTree($ctype_name, $parent_id=1, $level=1) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $parent = $this->getCategory($ctype_name, $parent_id);

        $this->
            filterGt('ns_left', $parent['ns_left'])->
            filterLt('ns_right', $parent['ns_right']);

        if ($level){
            $this->filterLtEqual('ns_level', $parent['ns_level'] + $level);
        }

        $this->orderBy('ns_left');

        $this->useCache('content.categories');

        return $this->get($table_name);

    }

//============================================================================//
//============================================================================//

    public function getCategoryPath($ctype_name, $category) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

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

        return $this->get($table_name);

    }

//============================================================================//
//============================================================================//

    public function addCategory($ctype_name, $category){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->db->nestedSets->setTable($table_name);

        $category['id'] = $this->db->nestedSets->addNode($category['parent_id']);

        if (!$category['id']){ return false; }

        $category['title'] = $this->db->escape($category['title']);

        $this->update($table_name, $category['id'], $category);

        $category['path'] = $this->getCategoryPath($ctype_name, $category);

        $category['slug'] = $this->getCategorySLUG($category);

        $this->update($table_name, $category['id'], array(
            'slug' => $category['slug']
        ));

        cmsCache::getInstance()->clean('content.categories');

        return $category;

    }

//============================================================================//
//============================================================================//

    public function updateCategory($ctype_name, $id, $category){

        cmsCache::getInstance()->clean('content.categories');

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $category_old = $this->getCategory($ctype_name, $id);

        if ($category_old['parent_id'] != $category['parent_id']){
            $this->db->nestedSets->setTable($table_name);
            $this->db->nestedSets->moveNode($id, $category['parent_id']);
        }

		$this->update($table_name, $id, $category);

        $category['id'] = $id;
        $category['path'] = $this->getCategoryPath($ctype_name, array('id' => $id));
        $category['slug'] = $this->getCategorySLUG($category);

        $this->update($table_name, $id, array(
            'slug' => $category['slug']
        ));

        $subcats = $this->getSubCategoriesTree($ctype_name, $id, false);

        if ($subcats){
            foreach($subcats as $subcat){
                $subcat['path'] = $this->getCategoryPath($ctype_name, array('id' => $subcat['id']));
                $subcat['slug'] = $this->getCategorySLUG($subcat);
                $this->update($table_name, $subcat['id'], array('slug' => $subcat['slug']));
            }
        }

        return $category;

    }

//============================================================================//
//============================================================================//

    public function updateCategoryTree($ctype_name, $tree, $categories_count){

        cmsCache::getInstance()->clean('content.categories');

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->updateCategoryTreeNode($ctype_name, $tree);
        $this->updateCategoryTreeNodeSlugs($ctype_name, $tree);

        $root_keys = array(
            'ns_left' => 1,
            'ns_right' => 1 + ($categories_count*2) + 1
        );

        $this->update($table_name, 1, $root_keys);

        return true;

    }

    public function updateCategoryTreeNode($ctype_name, $tree){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        foreach($tree as $node){

            $this->update($table_name, $node['key'], array(
                'parent_id' => $node['parent_key'],
                'ns_left' => $node['left'],
                'ns_right' => $node['right'],
                'ns_level' => $node['level'],
            ));

            if (!empty($node['children'])){
                $this->updateCategoryTreeNode($ctype_name, $node['children']);
            }

        }

        return true;

    }

    public function updateCategoryTreeNodeSlugs($ctype_name, $tree){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        foreach($tree as $node){

            $path = $this->getCategoryPath($ctype_name, array(
                'id' => $node['key'],
                'parent_id' => $node['parent_key'],
                'ns_left' => $node['left'],
                'ns_right' => $node['right'],
                'ns_level' => $node['level']
            ));

            $slug = $this->getCategorySLUG(array(
                'path' => $path,
                'title' => $node['title']
            ));

            $this->update($table_name, $node['key'], array(
                'slug' => $slug
            ));

            if (!empty($node['children'])){
                $this->updateCategoryTreeNodeSlugs($ctype_name, $node['children']);
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

        $table_name = $this->table_prefix . $ctype_name . '_cats';

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

    public function update($table_name, $id, $data, $skip_check_fields = false){
        $this->filterEqual('id', $id);
        return $this->updateFiltered($table_name, $data, $skip_check_fields);
    }

    public function updateFiltered($table_name, $data, $skip_check_fields = false){
        $where = $this->where;
        $this->resetFilters();
        return $this->db->update($table_name, $where, $data, $skip_check_fields);
    }

//============================================================================//
//============================================================================//

    public function insert($table_name, $data){
        return $this->db->insert($table_name, $data);
    }

    public function insertOrUpdate($table_name, $insert_data, $update_data = false){
        return $this->db->insertOrUpdate($table_name, $insert_data, $update_data);
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

        $this->select       = array('i.*');
        $this->group_by     = '';
        $this->order_by     = '';
        $this->index_action = '';
        $this->limit        = '';
        $this->join         = '';
        $this->distinct     = '';
        $this->straight_join = '';

		if ($this->keep_filters) { return $this; }

		$this->filter_on    = false;
		$this->where        = '';
        $this->privacy_filtered = false;
        $this->approved_filtered = false;

        return $this;

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

    public function filterEqual($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_null($value)){
            $this->filter($field.' IS NULL');
        } else {
            $value = $this->db->escape($value);
            $this->filter("$field = '$value'");
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

    public function filterGt($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field > '$value'");
        return $this;
    }

    public function filterLt($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field < '$value'");
        return $this;
    }

    public function filterGtEqual($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field >= '$value'");
        return $this;
    }

    public function filterLtEqual($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
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

    public function filterIn($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_array($value)){
            foreach($value as $k=>$v){
                $v = $this->db->escape($v);
                $value[$k] = "'{$v}'";
            }
            $value = implode(',', $value);
        } else {
            $value = $this->db->escape($value);
            $value = "'{$value}'";
        }
        $this->filter("{$field} IN ({$value})");
        return $this;
    }

    public function filterNotIn($field, $value){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        if (is_array($value)){
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

    public function filterRelated($field, $value, $lang = false){

        if(mb_strlen($value) <= 3){
            return $this->filterLike($field, $this->db->escape($value).'%');
        }

        $query = array();

        $words = explode(' ', str_replace(array('"','\'','+','*','-','%',')','(','.',',','!','?'), ' ', $value));

        $stopwords = string_get_stopwords($lang ? $lang : cmsConfig::get('language'));

        foreach($words as $word){

            $word = mb_strtolower(trim($word));

            if (mb_strlen($word)<3 || is_numeric($word)) { continue; }
            if($stopwords && in_array($word, $stopwords)){ continue; }
            if (mb_strlen($word)==3) { $query[] = $this->db->escape($word); continue; }

            if (mb_strlen($word) >= 12) {
                $word = mb_substr($word, 0, mb_strlen($word) - 3).'*';
            } else if (mb_strlen($word) >= 10) {
                $word = mb_substr($word, 0, mb_strlen($word) - 2).'*';
            } else if (mb_strlen($word) >= 6) {
                $word = mb_substr($word, 0, mb_strlen($word) - 1).'*';
            }

            $query[] = $this->db->escape($word);

        }

        if (empty($query)) {
            $ft_query = '\"' . $this->db->escape($value) . '\"';
        } else {

            usort($query, function ($a, $b){
                return mb_strlen($b)-mb_strlen($a);
            });
            $query = array_slice($query, 0, 5);

            $ft_query  = '>\"' . $this->db->escape($value).'\" <';
            $ft_query .= implode(' ', $query);
        }

        if (strpos($field, '.') === false){ $field = 'i.' . $field; }

        $search_param = "MATCH({$field}) AGAINST ('{$ft_query}' IN BOOLEAN MODE)";

        $this->select($search_param, 'fsort');

        $this->order_by = 'fsort desc';

        return $this->filter($search_param);

    }

    public function filterCategory($ctype_name, $category, $is_recursive=false){

		$table_name      = $this->table_prefix . $ctype_name . '_cats';
        $bind_table_name = $table_name . '_bind';

        if (!$is_recursive){

            $this->joinInner($bind_table_name, 'b FORCE INDEX (item_id)', 'b.item_id = i.id')->filterEqual('b.category_id', $category['id']);

        } else {

            // для корневой категории фильтрация не нужна
            if(!$category['parent_id']){
                return $this;
            }

            /**
             * Нам нужны только уникальные значения
             * Закомментировано потому что DISTINCT дает нагрузку
             * @todo сделать анализ кол-ва записей, вложенности категорий и динамически включать или отключать
             * В общих случаях, когда дерево категорий имеет первый и второй уровень раскомментировать не нужно
             * Для малых БД, где повсеместно используется принадлежность к нескольким категориям ниже второго уровня
             * имеет смысл раскомментировать строку ниже
             */
            //$this->distinctSelect();

            $this->joinInner($bind_table_name, 'b FORCE INDEX (item_id)', 'b.item_id = i.id');
            $this->joinInner($table_name, 'c', 'c.id = b.category_id');
            $this->filterGtEqual('c.ns_left', $category['ns_left']);
            $this->filterLtEqual('c.ns_right', $category['ns_right']);

        }

        return $this;

    }

	public function filterCategoryId($ctype_name, $category_id, $is_recursive=false){

		if (!$is_recursive){

            if($category_id){
                return $this->filterCategory($ctype_name, array('id'=>$category_id));
            }

		} else {

			$category = $this->getCategory($ctype_name, $category_id);
            if($category){
                return $this->filterCategory($ctype_name, $category, true);
            }

		}

        return $this;

	}

    public function disablePrivacyFilter(){
        $this->privacy_filter_disabled = true;
        return $this;
    }

    public function enablePrivacyFilter(){
        $this->privacy_filter_disabled = false;
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

        return $this->filterEqual('i.is_private', 0);

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
        return $this->filterIsNull('is_parent_hidden');
    }

    public function filterFriends($user_id){

        $this->joinInner('{users}_friends', 'fr', 'fr.friend_id = i.user_id');

        $this->filterEqual('fr.user_id', intval($user_id));
        $this->filterEqual('fr.is_mutual', 1);

        return $this;

    }

    public function filterFriendsPrivateOnly($user_id){

        // фильтр приватности при этом не нужен
        $this->privacy_filtered = true;

        return $this->filterEqual('i.is_private', 1)->filterFriends($user_id);

    }

    public function applyDatasetFilters($dataset, $ignore_sorting=false){

        if (!empty ($dataset['filters'])){

            foreach($dataset['filters'] as $filter){

                if (isset($filter['callback']) && is_callable($filter['callback'])){
                    $filter['callback']($this, $dataset); continue;
                }

                if (!isset($filter['value'])) { continue; }
                if (($filter['value'] === '') && !in_array($filter['condition'], array('nn', 'ni'))) { continue; }
                if (empty($filter['condition'])) { continue; }

                if ($filter['value'] !== '') { $filter['value'] = string_replace_user_properties($filter['value']); }

                switch($filter['condition']){

                    // общие условия
                    case 'eq': $this->filterEqual($filter['field'], $filter['value']); break;
                    case 'gt': $this->filterGt($filter['field'], $filter['value']); break;
                    case 'lt': $this->filterLt($filter['field'], $filter['value']); break;
                    case 'ge': $this->filterGtEqual($filter['field'], $filter['value']); break;
                    case 'le': $this->filterLtEqual($filter['field'], $filter['value']); break;
                    case 'nn': $this->filterNotNull($filter['field']); break;
                    case 'ni': $this->filterIsNull($filter['field']); break;

                    // строки
                    case 'lk': $this->filterLike($filter['field'], '%'.$filter['value'].'%'); break;
                    case 'lb': $this->filterLike($filter['field'], $filter['value'] . '%'); break;
                    case 'lf': $this->filterLike($filter['field'], '%' . $filter['value']); break;

                    // даты
                    case 'dy': $this->filterDateYounger($filter['field'], $filter['value']); break;
                    case 'do': $this->filterDateOlder($filter['field'], $filter['value']); break;

                }

            }

        }

        if (!empty($dataset['sorting']) && !$ignore_sorting){
            $this->orderByList($dataset['sorting']);
        }

        if(!empty($dataset['index'])){
            $this->forceIndex($dataset['index'], 2);
        }

        return true;

    }

    public function selectList($fields, $is_this_only = false){
        if($is_this_only){ $this->select = array(); }
        foreach($fields as $field => $alias){
            $this->select($field, $alias);
        }
        return $this;
    }

    public function select($field, $as=false){
        $this->select[] = $as ? $field.' as '.$as : $field;
        return $this;
    }

    public function selectOnly($field, $as=false){
        $this->select = array();
        $this->select[] = $as ? $field.' as '.$as : $field;
        return $this;
    }

    public function join($table_name, $as, $on){
        return $this->joinInner($table_name, $as, $on);
    }

    public function joinInner($table_name, $as, $on){
        $this->join .= self::INNER_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$on.PHP_EOL;
        return $this;
    }

    public function joinLeft($table_name, $as, $on){
        $this->join .= self::LEFT_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$on.PHP_EOL;
        return $this;
    }

    public function joinExcludingLeft($table_name, $as, $right_key, $left_key, $join_where = ''){
        $this->join .= self::LEFT_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$left_key.'='.$right_key.($join_where ? ' AND '.$join_where : '').PHP_EOL;
        $this->filter($right_key.' IS NULL');
        return $this;
    }

    public function joinRight($table_name, $as, $on){
        $this->join .= self::RIGHT_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$on.PHP_EOL;
        return $this;
    }

    public function joinExcludingRight($table_name, $as, $right_key, $left_key, $join_where = ''){
        $this->join .= self::RIGHT_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$left_key.'='.$right_key.($join_where ? ' AND '.$join_where : '').PHP_EOL;
        $this->filter($left_key.' IS NULL');
        return $this;
    }

    public function joinLeftOuter($table_name, $as, $on){
        $this->join .= self::LEFT_OUTER_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$on.PHP_EOL;
        return $this;
    }

    public function joinRightOuter($table_name, $as, $on){
        $this->join .= self::RIGHT_OUTER_JOIN.' {#}'.$table_name.' as '.$as.' ON '.$on.PHP_EOL;
        return $this;
    }

    public function joinUser($on_field='user_id', $user_fields=array(), $join_direction=false){

        if (!$user_fields){
            $user_fields = array(
                'u.nickname' => 'user_nickname',
                'u.avatar'   => 'user_avatar'
            );
        }

        foreach($user_fields as $field => $alias){
            $this->select($field, $alias);
        }

		switch ($join_direction){

			case 'left':
				$this->joinLeft('{users}', 'u', 'u.id = i.'.$on_field);
				break;

			case 'right':
				$this->joinRight('{users}', 'u', 'u.id = i.'.$on_field);
				break;

			default:
				$this->join('{users}', 'u', 'u.id = i.'.$on_field);
				break;

		}

        return $this;

    }

	public function joinUserLeft($on_field='user_id', $user_fields=array()){
		return $this->joinUser($on_field, $user_fields, 'left');
	}

	public function joinUserRight($on_field='user_id', $user_fields=array()){
		return $this->joinUser($on_field, $user_fields, 'right');
	}

    public function groupBy($field){
        if (strpos($field, '.') === false){ $field = 'i.' . $field; }
        $this->group_by = $field;
        return $this;
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

    public function orderBy($field, $direction='', $is_force_index_by_field = false){
        if(strpos($field, '(') !== false){ return $this; } // в названии поля не может быть функции
        if($direction){
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        }
        if (strpos($field, '.') === false){ $field = 'i.'.$field; }
        $this->order_by = $field.' '.$direction;
        return $this;
    }

    public function orderByList($list){

		$this->order_by = '';

		if (is_array($list)){

			foreach($list as $o){

                if(strpos($o['by'], '(') !== false){ continue; }

				$field     = $o['by'];
                $direction = strtolower($o['to']) === 'desc' ? 'desc' : 'asc';

                if (strpos($field, '.') === false){ $field = 'i.'.$field; }
				if ($this->order_by) { $this->order_by .= ', '; }
				$this->order_by .= $field.' '.$direction;

			}

		}

		return $this;

    }

    public function limit($from, $howmany=0) {
        $this->limit = (int)$from;
        $howmany     = (int)$howmany;
        if ($this->limit < 0) { $this->limit = 0; }
        if ($howmany){
            if ($howmany <= 0){ $howmany = 15; }
            $this->limit .= ', '. $howmany;
        }
        return $this;
    }

    public function limitPage($page, $perpage=0) {
        $page    = (int) $page;
        $perpage = (int) $perpage;
        if ($perpage <= 0) { $perpage = $this->perpage; }
        $this->limit(($page-1)*$perpage, $perpage);
        return $this;
    }

    public function limitPagePlus($page, $perpage=0) {
        $page    = (int) $page;
        $perpage = (int) $perpage;
        if ($perpage <= 0) { $perpage = $this->perpage; }
        $this->limit(($page-1)*$perpage, $perpage+1);
        return $this;
    }

    public function setPerPage($perpage){
        $this->perpage = (int)$perpage;
        return $this;
    }

//============================================================================//
//============================================================================//

    public function getField($table_name, $row_id, $field_name){

        $this->filterEqual('id', $row_id);
        return $this->getFieldFiltered($table_name, $field_name);

    }

    public function getFieldFiltered($table_name, $field_name){

        $this->select = array('i.'.$field_name.' as '.$field_name);

        $this->table = $table_name;

        $this->limit(1);

        $sql = $this->getSQL();

        $this->resetFilters();

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){ return false; }

        $item = $this->db->fetchAssoc($result);

        $this->db->freeResult($result);

        return $item[ $field_name ];

    }

//============================================================================//
//============================================================================//

    public function getItem($table_name, $item_callback=false){

        $select = implode(', ', $this->select);

        $sql = "SELECT {$select}
                FROM {#}{$table_name} i
                {$this->index_action}";

        if ($this->join){ $sql .= $this->join; }

        if ($this->where){ $sql .= 'WHERE '.$this->where.PHP_EOL; }

        if ($this->order_by){ $sql .= 'ORDER BY '.$this->order_by.PHP_EOL; }

        $sql .= 'LIMIT 1';

        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($item = $cache->get($cache_key))){
                $this->stopCache();
                return $item;
            }

        }

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){ return false; }

        $item = $this->db->fetchAssoc($result);

        if(is_callable($item_callback)){
            $item = call_user_func_array($item_callback, array($item, $this));
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key){
            $cache->set($cache_key, $item);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        return $item;

    }

    public function getItemById($table_name, $id, $item_callback=false){
        $this->filterEqual('id', $id);
        return $this->getItem($table_name, $item_callback);
    }

    public function getItemByField($table_name, $field_name, $field_value, $item_callback=false){
        $this->filterEqual($field_name, $field_value);
        return $this->getItem($table_name, $item_callback);
    }

//============================================================================//
//============================================================================//

    public function getCount($table_name, $by_field='id'){

        $sql = "SELECT {$this->straight_join} COUNT({$this->distinct} i.{$by_field} ) as count
                FROM {#}{$table_name} i
                {$this->index_action}";

        if ($this->join){ $sql .= $this->join; }

        if ($this->where){ $sql .= 'WHERE '.$this->where.PHP_EOL; }

        if ($this->group_by){ $sql .= 'GROUP BY '.$this->group_by.PHP_EOL; }

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($result = $cache->get($cache_key))){
                $this->stopCache();
                return $result;
            }

        }

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){
            $count = 0;
        } else {
            $item = $this->db->fetchAssoc($result);
            $count = (int)$item['count'];
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key){
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
     * @return array
     */
    public function get($table_name, $item_callback=false, $key_field='id'){

        $this->table = $table_name;

        $items = $_items = array();

        $sql = $this->getSQL();

        // сбрасываем фильтры
        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);

            $cache = cmsCache::getInstance();

            $_items = $cache->get($cache_key);

            if ($_items !== false){

                $this->stopCache();

                // обрабатываем коллбэком
                if (is_callable($item_callback)){

                    foreach ($_items as $key => $item) {

                        $item = call_user_func_array($item_callback, array($item, $this));
                        if ($item === false){ continue; }

                        $items[$key] = $item;

                    }

                } else {
                    return $_items;
                }

                return $items;

            }

        }

        $result = $this->db->query($sql);

        // если запрос ничего не вернул, возвращаем ложь
        if (!$this->db->numRows($result)){ return false; }

        // перебираем все вернувшиеся строки
        while($item = $this->db->fetchAssoc($result)){

            $key = $key_field ? $item[$key_field] : false;

            // для кеша формируем массив без обработки коллбэком
            if ($this->cache_key){
                if ($key){
                    $_items[$key] = $item;
                } else {
                    $_items[] = $item;
                }
            }

            // если задан коллбек для обработки строк,
            // то пропускаем строку через него
            if (is_callable($item_callback)){
                $item = call_user_func_array($item_callback, array($item, $this));
                if ($item === false){ continue; }
            }

            // добавляем обработанную строку в результирующий массив
            if ($key){
                $items[$key] = $item;
            } else {
                $items[] = $item;
            }

        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        // сохраняем не обработанный коллбэком массив
        if ($this->cache_key){
            $cache->set($cache_key, $_items);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        // возвращаем строки
        return $items;

    }

//============================================================================//
//============================================================================//

    public function getSQL(){

        $select = implode(', ', $this->select);

        $sql = "SELECT {$this->distinct} {$this->straight_join} {$select}
                FROM {#}{$this->table} i
                {$this->index_action}";

        if ($this->join){ $sql .= $this->join; }

        if ($this->where){ $sql .= 'WHERE '.$this->where.PHP_EOL; }

        if ($this->group_by){ $sql .= 'GROUP BY '.$this->group_by.PHP_EOL; }

        if ($this->order_by){ $sql .= 'ORDER BY '.$this->order_by.PHP_EOL; }

        if ($this->limit){ $sql .= 'LIMIT '.$this->limit.PHP_EOL; }

        return $sql;

    }

//============================================================================//
//============================================================================//

    public function getMax($table, $field, $default=0){

        $sql = "SELECT i.{$field} as {$field}
                FROM {#}{$table} i
                ";

        if ($this->where){ $sql .= 'WHERE '.$this->where.PHP_EOL; }

        $sql .= "ORDER BY i.{$field} DESC
                 LIMIT 1";

        $result = $this->db->query($sql);

        $this->resetFilters();

        if (!$this->db->numRows($result)){ return $default; }

        $max = $this->db->fetchAssoc($result);

        $this->db->freeResult($result);

        return $max[$field];

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
     * @param string $table_name
     * @param string $where
     * @return bool
     */
    public function reorder($table_name){

        $sql = "SELECT i.id as id, i.ordering as ordering
                FROM {#}{$table_name} i
                ";

        if ($this->where){ $sql .= 'WHERE '.$this->where.PHP_EOL; }

        $sql .= 'ORDER BY ordering';

        $result = $this->db->query($sql);

        $this->resetFilters();

        if (!$this->db->numRows($result)){
            $this->db->freeResult($result);
            return false;
        }

        $ordering = 0;

        while($item = $this->db->fetchAssoc($result)){

            $ordering += 1;
            $this->db->query("UPDATE {#}{$table_name} SET ordering = {$ordering} WHERE id = '{$item['id']}'");

        }

        $this->db->freeResult($result);

        return true;

    }

    /**
     * Расставляет порядковые номера для списка из ID записей
     * @param string $table_name
     * @param string $list
     * @param array $additional_fields Список дополнительных полей и их значений, которые нужно обновлять вместе с ordering
     * @return bool
     */
    public function reorderByList($table_name, $list, $additional_fields=false){

        $ordering = 0;

        $additional_set = array();

        if (is_array($additional_fields)){
            foreach($additional_fields as $field=>$value){
                $value = $this->db->escape($value);
                $additional_set[] = "{$field} = '{$value}'";
            }
        }

        if ($additional_set){
            $additional_set = ', ' . implode(', ', $additional_set);
        } else {
            $additional_set = '';
        }

        foreach($list as $id){

            $ordering += 1;

            $id = $this->db->escape($id);

            $query = "UPDATE {#}{$table_name}
                      SET ordering = '{$ordering}' {$additional_set}
                      WHERE id = '{$id}'";

            $this->db->query($query);

        }

        return true;

    }

//============================================================================//
//============================================================================//

    /**
     * Применяет к модели фильтры, переданные из просмотра
     * таблицы со списком записей
     * @param array $grid
     * @param array $filter
     * @return bool
     */
    public function applyGridFilter($grid, $filter){

        // применяем сортировку
        if (!empty($filter['order_by'])) {
            if (!empty($grid['columns'][$filter['order_by']]['order_by'])){
                $filter['order_by'] = $grid['columns'][$filter['order_by']]['order_by'];
            }
            $this->orderBy($filter['order_by'], $filter['order_to']);
        }

        // устанавливаем страницу
        if (!empty($filter['page'])){
            $perpage = !empty($filter['perpage']) ? intval($filter['perpage']) : $this->perpage;
            $this->limitPage(intval($filter['page']), $perpage);
        }

        //
        // проходим по каждой колонке таблицы
        // и проверяем не передан ли фильтр для нее
        //
        foreach($grid['columns'] as $field => $column){
            if (!empty($column['filter']) && $column['filter'] != 'none' && isset($filter[$field])){

                if ($filter[$field] || (string)$filter[$field] === '0'){

                    if (!empty($column['filter_by'])){
                        $filter_field = $column['filter_by'];
                    } else {
                        $filter_field = $field;
                    }

                    switch ($column['filter']){
                        case 'exact': $this->filterEqual($filter_field, $filter[$field]); break;
                        case 'like': $this->filterLike($filter_field, "%{$filter[$field]}%"); break;
                        case 'date':
							$date = date('Y-m-d', strtotime($filter[$field]));
							$this->filterLike($filter_field, "%{$date}%"); break;
                    }

                }

            }
        }

        return $this;

    }

//============================================================================//
//============================================================================//

    public function increment($table, $field, $step=1){

        $sign = $step > 0 ? '+' : '-';
        $step = abs($step);

        $sql = "UPDATE {#}{$table} i
                SET i.{$field} = i.{$field} {$sign} {$step}
                ";

        if ($this->where){ $sql .= 'WHERE '.$this->where; }

        $this->resetFilters();

        return $this->db->query($sql);

    }

    public function decrement($table, $field, $step=1){
        return $this->increment($table, $field, $step * -1);
    }

    public function deleteController($name) {

		if(is_numeric($name)){
            $controller = $this->getItemById('controllers', $name);
            $name = $controller['name'];
		}

        $this->filterEqual('listener', $name)->deleteFiltered('events');

        cmsCache::getInstance()->clean('events');

        return $this->filterEqual('name', $name)->deleteFiltered('controllers');

    }

    public function fieldsAfterStore($item, $fields, $action = 'add') {

        foreach($fields as $field){
            $field['handler']->afterStore($item, $this, $action);
        }

        return $this;

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
     * @param array $array
     * @return string
     */
    public static function arrayToYaml($input_array, $indent = 2, $word_wrap = 40) {

        if(!empty($input_array)){

            foreach ($input_array as $key => $value) {
                $_k = str_replace(array('[',']'), '', $key); // был фатальный баг, если в ключах эти символы
                $array[$_k] = $value;
            }

        } else {
            $array = array();
        }

        return Spyc::YAMLDump($array, $indent, $word_wrap);

    }

    /**
     * Преобразует YAML в массив
     * @param string $yaml
     * @return array
     */
    public static function yamlToArray($yaml) {

        return Spyc::YAMLLoadString($yaml);

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
        if(isset(self::$cached[$key])){
            return self::$cached[$key];
        }
        return null;
    }

}
