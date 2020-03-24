<?php

class cmsDatabase {

    private static $instance;

    /**
     * Префикс таблиц
     * @var string
     */
	public $prefix;

    /**
     * deprecated, use cmsDebugging
     */
    public $query_count = 0;
    public $query_list = array();

    /**
     * Массив кешированного списка полей для запрашиваемых таблиц
     * @var array
     */
    private $table_fields = array();

    /**
     * Объект, представляющий подключение к серверу MySQL
     * @var \mysqli
     */
    private $mysqli;

    /**
     * Время соединения с базой
     * @var integer
     */
    private $init_start_time;

    /**
     * Время, через которое при PHP_SAPI == 'cli' нужно сделать реконнект
     * для случаев, когда mysql.connect_timeout по дефолту (60 с) и переопределить это поведение нельзя
     * @var integer
     */
    private $reconnect_time = 60;

    /**
     * Ошибка подключения к базе
     * @var boolean|string
     */
    private $connect_error = false;

    /**
     *
     * @var boolean|null
     */
    public $query_quiet = null;

    /**
     * Настройки базы данных
     * @var array
     */
    private $options = [
        'db_charset' => 'utf8'
    ];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

//============================================================================//
//============================================================================//

	public function __construct($options = array()){

        $this->setOptions($options ? $options : cmsConfig::getInstance()->getAll());

        $this->connect();

	}

    public function setOptions($options) {
        $this->options = array_merge($this->options, $options);
    }

    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

	public function __destruct(){
        if($this->ready()){

            // откатываемся, если была транзакция и ошибка в ней
            if(!$this->isAutocommitOn() && $this->mysqli->errno){
                $this->rollback();
            }

            $this->mysqli->close();

        }
	}

    public function __get($name) {
        if ($name == 'nestedSets') {
            $this->nestedSets = new cmsNestedsets($this);
            return $this->nestedSets;
        }
    }

    private function connect() {

        if (!empty($this->options['debug'])){
            cmsDebugging::pointStart('db');
        }

        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
            $this->mysqli = new mysqli($this->options['db_host'], $this->options['db_user'], $this->options['db_pass'], $this->options['db_base']);
        } catch (Exception $e) {

            $this->connect_error = $e->getMessage();
            return false;

        }

        $this->mysqli->set_charset($this->options['db_charset']);

        if(!empty($this->options['clear_sql_mode'])){
            $this->mysqli->query("SET sql_mode=''");
        }

        if (!empty($this->options['debug'])){
            cmsDebugging::pointProcess('db', array(
                'data' => 'Database connection'
            ), 3);
        }

        $this->setTimezone();

		$this->prefix = $this->options['db_prefix'];

        $this->init_start_time = time();

        return true;

    }

	public function ready(){
        return $this->connect_error === false;
    }

	public function connectError(){
        return $this->connect_error;
    }

	public function reconnect($is_force = false){

        if ($is_force || !$this->mysqli->ping()){

            $this->mysqli->close();

            return $this->connect();

        }

		return true;

	}

    public function getStat(){

        if (isset($this->mysqli->stat)){
            return $this->mysqli->stat;
        }

		return '';

	}

    public function setTimezone(){
        $this->query("SET `time_zone` = '%s'", date('P')); return $this;
    }

    public function setLcMessages(){
        if(defined('LC_LANGUAGE_TERRITORY')){
            $this->mysqli->query("SET lc_messages = '".LC_LANGUAGE_TERRITORY."'");
        }
        return $this;
    }

//============================================================================//
//============================================================================//

    public function autocommitOn() {
        $this->mysqli->autocommit(true); return $this;
    }

    public function autocommitOff() {
        $this->mysqli->autocommit(false); return $this;
    }

    public function isAutocommitOn() {

        $result = $this->mysqli->query('SELECT @@autocommit');

		if($result){

			$row = $result->fetch_row();

            $result->free();

            return isset($row[0]) && $row[0] == 1;

		}

        return false;

    }

    public function rollback() {
        $this->mysqli->rollback(); return $this;
    }

    public function commit() {
        $this->mysqli->commit(); return $this;
    }

    public function beginTransaction() {
        $this->mysqli->begin_transaction(); return $this;
    }

//============================================================================//
//============================================================================//

	/**
	 * Подготавливает строку перед запросом
	 *
	 * @param string $string
	 * @return string
	 */
	public function escape($string){
		return $this->mysqli->real_escape_string($string);
	}

    /**
     * Формирует префиксы таблиц в SQL запросе
     * @param string $sql
     * @return string
     */
    public function replacePrefix($sql) {
        return str_replace([
            '{#}{users}', '{users}', '{#}'
        ], [
            $this->options['db_users_table'], $this->options['db_users_table'], $this->prefix
        ], $sql);
    }

    /**
     * Выполняет запрос в базе
     * @param string $sql Строка запроса
     * @param array|string $params Аргументы запроса, которые будут переданы в vsprintf
     * @param boolean $quiet В случае ошибки запроса отдавать false, а не "умирать"
     * @return boolean
     */
	public function query($sql, $params = false, $quiet = false){

        if (!empty($this->options['debug'])){
            cmsDebugging::pointStart('db');
        }

        $sql = $this->replacePrefix($sql);

        if ($params){

            if (!is_array($params)){
                $params = array($params);
            }

            foreach($params as $index=>$param){
                if (!is_numeric($param)){
                    $params[$index] = $this->escape($param);
                }
            }

            $sql = vsprintf($sql, $params);

        }

        if(PHP_SAPI == 'cli' && (time() - $this->init_start_time) >= $this->reconnect_time){
            $this->reconnect();
        }

        $result = $this->mysqli->query($sql);

        if (!empty($this->options['debug'])){
            cmsDebugging::pointProcess('db', array(
                'data' => $sql
            ));
        }

		if(!$this->mysqli->errno) { return $result; }

        if($quiet || $this->query_quiet === true) {

            error_log(sprintf(ERR_DATABASE_QUERY, $this->error()));

            return false;

        }

        cmsCore::error(sprintf(ERR_DATABASE_QUERY, $this->error()), $sql);

	}

//============================================================================//
//============================================================================//

    public function freeResult($result){
        $result->close();
    }

    public function affectedRows(){
        return $this->mysqli->affected_rows;
    }

    public function numRows($result){
        return $result->num_rows;
    }

    public function fetchAssoc($result) {
        return $result->fetch_assoc();
    }

    public function fetchRow($result) {
        return $result->fetch_row();
    }

    public function error() {
        return $this->mysqli->error;
    }

//============================================================================//
//============================================================================//

	/**
	 * Возвращает ID последней вставленной записи из таблицы
     * При работе с транзакциями вызывать необходимо
     * До коммита
     *
	 * @return integer
	 */
	public function lastId(){
		return $this->mysqli->insert_id;
	}

    /**
     * Возвращает все названия полей для таблицы
     * @param string $table
     * @return array
     */
    public function getTableFields($table) {

        if(isset($this->table_fields[$table])){
            return $this->table_fields[$table];
        }

		$result = $this->query("SHOW COLUMNS FROM `{#}{$table}`");

        $fields = array();

        while($data = $this->fetchAssoc($result)){
            $fields[] = $data['Field'];
        }

        $this->table_fields[$table] = $fields;

        return $fields;

    }

//============================================================================//
//============================================================================//

    /**
     * Подготавливает значение $value поля $field для вставки в запрос
     * @param string $field
     * @param string $value
     * @param boolean $array_as_json Переходная опция для миграции с Yaml на Json
     * @return string
     */
    public function prepareValue($field, $value, $array_as_json = false){

        $is_date_field = strpos($field, 'date_') === 0;

        // если значение поля - массив,
        // то преобразуем его в YAML
        if (is_array($value)){
            if($array_as_json){
                $value = "'". $this->escape(cmsModel::arrayToString($value)) ."'";
            } else {
                $value = "'". $this->escape(cmsModel::arrayToYaml($value)) ."'";
            }
        } else

        // если это поле даты и оно не установлено,
        // то используем текущее время
		if ($is_date_field && ($value === false)) { $value = 'NULL'; }  else
        if ($is_date_field && ($value == '' || is_null($value))) { $value = 'CURRENT_TIMESTAMP'; }  else

        // если это поле булево,
        // то преобразуем его в число
        if (is_bool($value)) { $value = (int)$value; } else

        // если значение поля не задано,
        // то запишем в базу NULL
        if ($value === '' || is_null($value)) { $value = 'NULL'; } else

        // если значение поля как результат функции
        if (is_callable($value) && ($value instanceof Closure)) { $value = $value($this); }

        else {

            $value = $this->escape(trim($value));
            $value = "'{$value}'";

        }

        return $value;

    }

//============================================================================//
//============================================================================//

    /**
     * Выполняет запрос UPDATE
     *
     * @param string $table Таблица
     * @param string $where Критерии запроса
	 * @param array $data Массив[Название поля] = значение поля
	 * @param boolean $skip_check_fields Не проверять наличие обновляемых полей
     * @param boolean $array_as_json Переходная опция для миграции с Yaml на Json
     * @return boolean
     */
	public function update($table, $where, $data, $skip_check_fields = false, $array_as_json = false){

		if(empty($data)){ return false; }

        if(!$skip_check_fields){
            $table_fields = $this->getTableFields($table);
        }

        $set = [];

		foreach ($data as $field=>$value) {
            if(!$skip_check_fields && !in_array($field, $table_fields)){
                continue;
            }
            $value = $this->prepareValue($field, $value, $array_as_json);
			$set[] = "`{$field}` = {$value}";
		}

        if(!$set){ return false; }

        $set = implode(', ', $set);

		$sql = "UPDATE {#}{$table} i SET {$set} WHERE {$where}";

		if ($this->query($sql)) { return true; } else { return false; }

	}

	/**
	 * Выполняет запрос INSERT
	 *
	 * @param string $table Таблица
	 * @param array $data Массив[Название поля] = значение поля
	 * @param boolean $skip_check_fields Не проверять наличие обновляемых полей
     * @param boolean $array_as_json Переходная опция для миграции с Yaml на Json
     * @param boolean $ignore Пропускать записи, если при вставке возникают ошибки (INSERT IGNORE)
	 * @return boolean|integer ID вставленной записи
	 */
	public function insert($table, $data, $skip_check_fields = false, $array_as_json = false, $ignore = false){

        if(empty($data) || !is_array($data)) { return false; }

        if(!$skip_check_fields){
            $table_fields = $this->getTableFields($table);
        }

        $fields = $values = [];

        foreach ($data as $field => $value){

            if(!$skip_check_fields && !in_array($field, $table_fields)){
                continue;
            }

            $fields[] = "`$field`";
            $values[] = $this->prepareValue($field, $value, $array_as_json);

        }

        if(!$fields){ return false; }

        $fields = implode(', ', $fields);
        $values = implode(', ', $values);

        $sql = "INSERT ".($ignore ? 'IGNORE ': '')."INTO {#}{$table} ({$fields})\nVALUES ({$values})";

        if ($this->query($sql)) { return $this->lastId(); }

		return false;

	}

	/**
	 * Выполняет запрос INSERT
     * при совпадении PRIMARY или UNIQUE ключа выполняет UPDATE вместо INSERT
	 *
	 * @param string $table Таблица
	 * @param array $data Массив данных для вставки в таблицу
	 * @param array $update_data Массив данных для обновления при совпадении ключей
	 * @return boolean|integer
	 */
	public function insertOrUpdate($table, $data, $update_data = false){

        $fields = array();
        $values = array();
        $set    = array();

        if (is_array($data)){

			foreach ($data as $field => $value){

                $value = $this->prepareValue($field, $value);

                $fields[] = "`$field`";
                $values[] = $value;

                if($update_data === false){
                    $set[] = "`{$field}` = {$value}";
                }

			}

            $fields = implode(', ', $fields);
            $values = implode(', ', $values);

			$sql = "INSERT INTO {#}{$table} ({$fields})\nVALUES ({$values})";

            if(is_array($update_data)){
                foreach ($update_data as $field=>$value) {

                    $value = $this->prepareValue($field, $value);

                    $set[] = "`{$field}` = {$value}";

                }
            }

            $set = implode(', ', $set);

            $sql .= " ON DUPLICATE KEY UPDATE {$set}";

			if ($this->query($sql)) { return $this->lastId(); }

		}

		return false;

	}

    /**
     * Выполняет запрос DELETE
     * @param string $table_name Таблица
     * @param string $where Критерии запроса
     * @return boolean
     */
	public function delete($table_name, $where){
        $where = str_replace('i.', '', $where);
        return $this->query("DELETE FROM {#}{$table_name} WHERE {$where}");
	}

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив со всеми строками полученными после запроса
     * @param string $table_name
     * @param string $where
     * @param string $fields
     * @param string $order
     * @return boolean|array
     */
    public function getRows($table_name, $where='1', $fields='*', $order='id ASC', $quiet = false){
		$sql = "SELECT {$fields} FROM {#}{$table_name} WHERE {$where} ORDER BY {$order}";
		$result = $this->query($sql, false, $quiet);
		if(!$this->mysqli->errno){
			$data=array();
			while($item = $this->fetchAssoc($result)){
				$data[] = $item;
			}
            $this->freeResult($result);
			return $data;
		} else {
			return false;
		}
	}

    /**
     * Возвращает массив с одной строкой из базы
     * @param string $table
     * @param string $where
     * @param string $fields
     * @param string $order
     * @return boolean|array
     */
	public function getRow($table, $where='1', $fields='*', $order=''){
		$sql = "SELECT {$fields} FROM {#}{$table} WHERE {$where}";
        if ($order){ $sql .= " ORDER BY {$order}"; }
        $sql .= " LIMIT 1";
		$result = $this->query($sql);
		if($result){
			$data = $this->fetchAssoc($result);
            $this->freeResult($result);
			return $data;
		} else {
			return false;
		}
	}

//============================================================================//
//============================================================================//

	/**
	 * Возвращает одно поле из таблицы в базе
	 *
	 * @param string $table
	 * @param string $where
	 * @param string $field
	 * @param string $order
	 * @return mixed
	 */
	public function getField($table, $where, $field, $order=''){

		$row = $this->getRow($table, $where, $field, $order);

        if(!$row){ return false; }

		return array_key_exists($field, $row) === true ? $row[$field] : false;

	}

	public function getFields($table, $where, $fields='*', $order=''){
		return $this->getRow($table, $where, $fields, $order);
	}

//============================================================================//
//============================================================================//

    /**
     * Возвращает количество строк выведенных запросом
     * @param string $table
     * @param string $where
     * @param integer $limit
     * @return boolean|integer
     */
	public function getRowsCount($table, $where='1', $limit=false){
		$sql = "SELECT COUNT(1) FROM {#}$table WHERE $where";
        if ($limit) { $sql .= " LIMIT {$limit}"; }
		$result = $this->query($sql);
		if($result){
			$row = $this->fetchRow($result);
            $count = $row[0];
            $this->freeResult($result);
            return $count;
		} else {
			return false;
		}
	}

//============================================================================//
//============================================================================//

	/**
	 * Расставляет правильные порядковые номера (ordering) у элементов NS
	 * @param string $table
	 */
	public function reorderNS($table){
		$sql = "SELECT * FROM {#}{$table} ORDER BY ns_left";
		$result = $this->query($sql);

		if ($this->numRows($result)){
			$level = array();
			while ($item = $this->fetchAssoc($result)){
				if (isset($level[$item['ns_level']])){
					$level[$item['ns_level']] += 1;
				} else {
					$level[] = 1;
				}
				$this->query("UPDATE {$table} SET ordering = ".$level[$item['ns_level']]." WHERE id=".$item['id']);
			}
		}

        $this->freeResult($result);
	}

//============================================================================//
//============================================================================//

    public function createTable($table_name, $structure, $engine='MYISAM'){

        $sql = "CREATE TABLE IF NOT EXISTS `{#}{$table_name}` (\n";

        $fcount = 0;
        $ftotal = sizeof($structure);

        $indexes = $fulltext = $unique = $indexes_created = array();

        foreach ($structure as $name=>$field){

            $fcount++;
            $sep = ($fcount == $ftotal) ? "\n" : ",\n";

            $default  = (!isset($field['default']) ? 'NULL' : "NOT NULL DEFAULT '{$field['default']}'");
            $unsigned = (!isset($field['unsigned']) ? '' : 'UNSIGNED');

            // обычный индекс
            if (isset($field['index'])) {

                if($field['index'] === true){
                    $indexes[$name] = array($name);
                } else if(is_string($field['index'])) {
                    $indexes[$field['index']][$field['composite_index']] = $name;
                } else if(is_array($field['index'])){
                    foreach ($field['index'] as $k => $i_name) {
                        $indexes[$i_name][$field['composite_index'][$k]] = $name;
                    }
                }

            }
            // уникальный индекс
            if (isset($field['unique'])) { $unique[] = $name; }
            // полнотекстовый индекс
            if (isset($field['fulltext'])) {

                if($field['fulltext'] === true){
                    $fulltext[$name] = array($name);
                }

            }

            switch ($field['type']){

                case 'primary':
                    $sql .= "\t`{$name}` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY{$sep}";
                break;

                case 'bool':
                    $sql .= "\t`{$name}` tinyint(1) UNSIGNED {$default}{$sep}";
                break;

                case 'timestamp':
                    $current = (isset($field['default_current']) && $field['default_current']==true) ? "NOT NULL DEFAULT CURRENT_TIMESTAMP" : 'NULL';
                    $sql .= "\t`{$name}` TIMESTAMP {$current}{$sep}";
                break;

                case 'tinyint':
                    $sql .= "\t`{$name}` TINYINT {$unsigned} {$default}{$sep}";
                break;

                case 'int':
                    if (!isset($field['size'])){ $field['size'] = 11; }
                    $sql .= "\t`{$name}` INT( {$field['size']} ) {$unsigned} {$default}{$sep}";
                break;

                case 'varchar':
                    if (!isset($field['size'])){ $field['size'] = 255; }
                    $sql .= "\t`{$name}` VARCHAR( {$field['size']} ) {$default}{$sep}";
                break;

                case 'text':
                    $sql .= "\t`{$name}` TEXT NULL{$sep}";
                break;

                case 'set':
                    $values = array_map(function($item){ return '"'.$item.'"'; }, $field['items']);
                    $values = implode(',', $values);
                    $sql .= "\t`{$name}` SET ({$values}) {$default}{$sep}";
                break;

                default: break;

            }

        }

        $sql .= ") ENGINE={$engine} DEFAULT CHARSET={$this->options['db_charset']}";

        $this->query($sql);

        foreach($indexes as $index_name=>$fields){

            if (in_array($index_name, $indexes_created)) { continue; }

            $this->addIndex($table_name, $fields, $index_name);

            $indexes_created[] = $index_name;

        }

        foreach($unique as $field){

            if (in_array($field, $indexes_created)) { continue; }

            $this->addIndex($table_name, $field, $field, 'UNIQUE');

            $indexes_created[] = $field;

        }

        foreach($fulltext as $index_name=>$fields){

            if (in_array($index_name, $indexes_created)) { continue; }

            $this->addIndex($table_name, $fields, $index_name, 'FULLTEXT');

            $indexes_created[] = $index_name;

        }

    }

//============================================================================//
//============================================================================//

    public function createCategoriesTable($table_name) {

        $sql = "CREATE TABLE `{#}{$table_name}` (
                  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `parent_id` int(11) UNSIGNED DEFAULT NULL,
                  `title` varchar(200) NULL DEFAULT NULL,
                  `description` text NULL DEFAULT NULL,
                  `slug` varchar(255) NULL DEFAULT NULL,
                  `slug_key` varchar(255) NULL DEFAULT NULL,
                  `seo_keys` varchar(256) DEFAULT NULL,
                  `seo_desc` varchar(256) DEFAULT NULL,
                  `seo_title` varchar(256) DEFAULT NULL,
                  `seo_h1` varchar(256) DEFAULT NULL,
                  `ordering` int(11) UNSIGNED DEFAULT NULL,
                  `ns_left` int(11) UNSIGNED DEFAULT NULL,
                  `ns_right` int(11) UNSIGNED DEFAULT NULL,
                  `ns_level` int(11) UNSIGNED DEFAULT NULL,
                  `ns_differ` varchar(32) NOT NULL DEFAULT '',
                  `ns_ignore` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
                  `allow_add` text,
                  `is_hidden` tinyint(1) UNSIGNED DEFAULT NULL,
                  `cover` tinytext,
                  PRIMARY KEY (`id`),
                  KEY `slug` (`slug`),
                  KEY `parent_id` (`parent_id`,`ns_left`),
                  KEY `ns_left` (`ns_level`,`ns_right`,`ns_left`),
                  KEY `ordering` (`ordering`)
                ) ENGINE={$this->options['db_engine']} DEFAULT CHARSET={$this->options['db_charset']}";

        $this->query($sql);

        $this->nestedSets->setTable($table_name);
        $this->nestedSets->addRootNode();

        return true;

    }

    public function createCategoriesBindsTable($table_name) {

        $sql = "CREATE TABLE `{#}{$table_name}` (
				  `item_id` int(11) UNSIGNED DEFAULT NULL,
				  `category_id` int(11) UNSIGNED DEFAULT NULL,
				  KEY `item_id` (`item_id`),
				  KEY `category_id` (`category_id`)
				) ENGINE={$this->options['db_engine']} DEFAULT CHARSET={$this->options['db_charset']}";

        $this->query($sql);

        return true;

    }

//============================================================================//
//============================================================================//

    public function dropTable($table_name){

        $sql = "DROP TABLE IF EXISTS {#}{$table_name}";

        $this->query($sql);

    }

    public function dropTableField($table_name, $field_name){

        $sql = "ALTER TABLE `{#}{$table_name}` DROP `{$field_name}`";

        $this->query($sql);

    }

    public function addTableField($table_name, $field_name, $sql) {

        if ($this->isFieldExists($table_name, $field_name)) {
            return false;
        }

        return $this->query("ALTER TABLE `{#}{$table_name}` ADD `{$field_name}` {$sql}");

    }

    public function isTableExists($table_name){

		$result = $this->query('show tables');

        $tables = [];

        while($data = $this->fetchRow($result)){
            $tables[] = $data[0];
        }

        $table_name = $this->replacePrefix('{#}'.$table_name);

		return in_array($table_name, $tables, true);

	}

//============================================================================//
//============================================================================//

    public function isFieldUnique($table_name, $field_name, $value, $exclude_row_id = false){

        $value = $this->escape(trim($value));

		$where = "(`{$field_name}` = '{$value}')";

		if ($exclude_row_id) { $where .= " AND (id <> '{$exclude_row_id}')"; }

        return !(bool)$this->getRowsCount($table_name, $where, 1);

    }

    public function isFieldExists($table_name, $field){

        $table_fields = $this->getTableFields($table_name);

        return in_array($field, $table_fields, true);

    }

//============================================================================//
//============================================================================//
    /**
     * Возвращает поля, участвующие в индексе или false, если индекса нет
     * если индекс составной, то поля будут упорядочены в массиве как в индексе
     * @param string $table Название таблицы без префикса
     * @param string $index_name Название индекса
     * @return array|boolean
     */
    public function getIndex($table, $index_name) {

		$result = $this->query("SHOW INDEX FROM  `{#}{$table}` WHERE `Key_name` =  '{$index_name}'");

		if ($this->numRows($result)){
			$fields = array();
			while ($i = $this->fetchAssoc($result)){
                $fields[] = $i['Column_name'];
			}
            return $fields;
		} else {
            return false;
        }

    }

    /**
     * Возвращает все индексы таблицы
     * @param string $table Название таблицы без префикса
     * @param string $index_type Тип индекса
     * @return boolean|array
     */
    public function getTableIndexes($table, $index_type=null) {

        $sql = "SHOW INDEX FROM  `{#}{$table}`";
        if($index_type){
            $sql .= " WHERE `Index_type` = '{$index_type}'";
        }

        $result = $this->query($sql);

		if ($this->numRows($result)){

			$indexes = array();

			while ($i = $this->fetchAssoc($result)){
                $indexes[$i['Key_name']][] = $i['Column_name'];
			}

            return $indexes;

		}

        return false;

    }

    /**
     * Проверяет, есть ли указанный индекс в таблице
     * @param string $table Название таблицы без префикса
     * @param string $index_name Название индекса
     * @return boolean
     */
    public function isIndexExists($table, $index_name) {
        return $this->getIndex($table, $index_name) !== false;
    }

    /**
     * Удаляет индекс из таблицы, если он там есть
     * @param string $table Название таблицы без префикса
     * @param string $index_name Название индекса
     * @return boolean
     */
    public function dropIndex($table, $index_name) {
        if($this->isIndexExists($table, $index_name)){
            $this->query("ALTER TABLE `{#}{$table}` DROP INDEX `{$index_name}`");
            return true;
        }
        return false;
    }

    /**
     * Добавляет индекс к таблице
     * @param string $table Название таблицы без префикса
     * @param array|string $fields Поле или поля, участвующие в индексе
     * @param string $index_name Название индекса. Если не передано, название будет по последнему элементу
     * @param string $index_type Тип индекса
     * @return boolean FALSE если индекс с таким названием уже есть
     */
    public function addIndex($table, $fields, $index_name='', $index_type='INDEX') {

        if(is_string($fields)){
            $fields = array($fields);
        }

        if(!$index_name){
            $index_name = end($fields);
        }

        if($this->isIndexExists($table, $index_name)){
            return false;
        }

        ksort($fields);

        $fields_str = '`'.implode('`, `', $fields).'`';

        $this->query("ALTER TABLE `{#}{$table}` ADD {$index_type} `{$index_name}` ({$fields_str})");

        return true;

    }

//============================================================================//
//============================================================================//

    public function importDump($file, $delimiter = ';'){

        clearstatcache();

        if (function_exists('opcache_invalidate')) { @opcache_invalidate($file, true); }

        if (!is_readable($file)){ return false; }

        @set_time_limit(0);

        $file = fopen($file, 'r');

        $query = []; $success = false;

        while (feof($file) === false){

            $query[] = fgets($file);

            if (preg_match('~' . preg_quote($delimiter, '~').'\s*$~iS', end($query)) === 1){

                $success = true;

                $query = trim(implode('', $query));

                $result = $this->query(str_replace(['InnoDB','CHARSET=utf8'], [$this->options['db_engine'],'CHARSET='.$this->options['db_charset']], $query));

                if ($result === false) {
                    return false;
                }

            }

            if (is_string($query) === true){
                $query = array();
            }

        }

        fclose($file);

        return $success;

    }

}
