<?php

namespace icms\traits\controllers\models;

use cmsCache;
use cmsEventsManager;
use cmsFormField;

/**
 * Трейт моделей для создания полей в таблицах
 * Функционала типов контента
 * Трейт использует контекст моделей
 */
trait fieldable {

    /**
     * Массив опций поля по умолчанию
     * @return array
     */
    protected function getDefaultContentFieldOptions() {
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

    /**
     * Формирует зависимости поля
     *
     * @param array $field Запись поля
     * @return array
     */
    private function formatFieldVisibleDepend($field) {

        if(empty($field['options']['visible_depend'])){
            return $field;
        }

        $field['visible_depend'] = [];

        foreach ($field['options']['visible_depend'] as $vd) {
            $field['visible_depend'][$vd['field']] = [$vd['action'] => explode(',', ''.$vd['value'])];
        }

        return $field;
    }

    /**
     * Коллбэк для записи поля
     *
     * @param array $item Запись поля
     * @param string $table Таблица контента
     * @param integer $item_id ID записи типа контента
     * @return array
     */
    protected function fieldCallback($item, $table, $item_id = 0) {

        $item['options']     = self::yamlToArray($item['options']);
        $item['options']     = array_merge($this->getDefaultContentFieldOptions(), $item['options']);
        $item['groups_read'] = self::yamlToArray($item['groups_read']);
        $item['groups_add']  = self::yamlToArray($item['groups_add']);
        $item['groups_edit'] = self::yamlToArray($item['groups_edit']);
        $item['filter_view'] = self::yamlToArray($item['filter_view']);

        $item = $this->formatFieldVisibleDepend($item);

        $rules = [];
        if ($item['options']['is_required']) { $rules[] = ['required']; }
        if ($item['options']['is_digits']) { $rules[] = ['digits']; }
        if ($item['options']['is_number']) { $rules[] = ['number']; }
        if ($item['options']['is_alphanumeric']) { $rules[] = ['alphanumeric']; }
        if ($item['options']['is_email']) { $rules[] = ['email']; }
        if (!empty($item['options']['is_url'])) { $rules[] = ['url']; }
        if (!empty($item['options']['is_regexp']) && !empty($item['options']['rules_regexp_str'])) {
            $rules[] = ['regexp', $item['options']['rules_regexp_str'], get_localized_value('rules_regexp_error', $item['options'])];
        }
        ;
        if ($item['options']['is_unique']) {
            if (!$item_id) {
                $rules[] = ['unique', $table, $item['name']];
            } else {
                $rules[] = ['unique_exclude', $table, $item['name'], $item_id];
            }
        }

        $item['rules'] = $rules;

        return $item;
    }

    /**
     * Возвращает объект поля
     *
     * @param array $field Запись поля из БД
     * @return cmsFormField Объект поля
     */
    protected function getContentFieldHandler($field) {

        $field_property = $field;

        $field_class = 'field' . \string_to_camel('_', $field['type']);

        $handler = new $field_class($field['name']);

        unset($field_property['type']);

        $handler->setOptions($field_property);

        return $handler;
    }

    /**
     * Возвращает поля типа контента
     * Предполагается, что таблица существует
     * Метод не проверяет переданный $ctype_name
     *
     * @param string $ctype_name Имя типа контента
     * @param integer|bool $item_id ID записи типа контента (если есть, необходим для валидации)
     * @param integer $enabled Только включенные
     * @param array $show_fields Только перечисленные поля
     * @return array
     */
    public function getContentFields(string $ctype_name, $item_id = 0, $enabled = true, $show_fields = []) {

        $this->useCache('content.fields.' . $ctype_name);

        $table_name = $this->getContentTypeTableName($ctype_name, '_fields');

        $this->selectTranslatedField('i.values', $table_name, 'default');

        if ($enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        $this->orderBy('ordering')->limit(false);

        $fields = $this->get($table_name, function($item, $model) use ($ctype_name, $item_id, $show_fields) {

            if($show_fields && !in_array($item['name'], $show_fields)){
                return false;
            }

            return $model->fieldCallback($item, $model->getContentTypeTableName($ctype_name), $item_id);

        }, 'name') ?: [];

        // чтобы сработала мультиязычность, если необходима
        // поэтому перебираем тут, а не выше
        foreach ($fields as $name => $field) {
            $fields[$name]['handler'] = $this->getContentFieldHandler($field);
        }

        return $fields;
    }

    /**
     * Возвращает поля, обязательные к заполнению
     *
     * @param string $ctype_name Имя типа контента
     * @return array
     */
    public function getRequiredContentFields(string $ctype_name) {

        $fields = $this->getContentFields($ctype_name);

        $req_fields = [];

        foreach ($fields as $field) {
            if ($field['options']['is_required']) {
                $req_fields[] = $field;
            }
        }

        return $req_fields;
    }

    /**
     * Возвращает одно поле по его id или имени
     * Предполагается, что таблица существует
     * Метод не проверяет переданный $ctype_name
     *
     * @param string $ctype_name Имя типа контента
     * @param integer|string $id ID поля: номер или имя
     * @param string $by_field В каком поле искать ID: если ищем по имени, то передать нужно name
     * @return array
     */
    public function getContentField(string $ctype_name, $id, $by_field = 'id') {

        $table_name = $this->getContentTypeTableName($ctype_name, '_fields');

        $this->useCache('content.fields.' . $ctype_name);

        return $this->getItemByField($table_name, $by_field, $id, function ($item, $model) use($ctype_name) {

            $item = $model->fieldCallback($item, $model->getContentTypeTableName($ctype_name));

            // К сожалению, разные названия (handler и parser) исторически сложились :(
            $item['parser'] = $this->getContentFieldHandler($item);

            return $item;
        });
    }

    /**
     * Возвращает одно поле по его имени
     *
     * @param string $ctype_name Имя типа контента
     * @param string $name Имя поля
     * @return array
     */
    public function getContentFieldByName(string $ctype_name, $name) {

        return $this->getContentField($ctype_name, $name, 'name');
    }

    /**
     * Проверяет, что поле существует
     *
     * @param string $ctype_name Имя типа контента
     * @param string $name Имя поля
     * @return bool
     */
    public function isContentFieldExists(string $ctype_name, $name) {

        return $this->getContentFieldByName($ctype_name, $name) ? true : false;
    }

    /**
     * Возвращет массив групп полей
     *
     * @param integer|string $ctype_id id типа контента или его имя
     * @return array
     */
    public function getContentFieldsets($ctype_id) {

        if (is_numeric($ctype_id)) {

            $ctype = $this->getContentType($ctype_id);

            if (!$ctype) {
                return false;
            }

            $ctype_name = $ctype['name'];

        } else {
            $ctype_name = $ctype_id;
        }

        $table_name = $this->getContentTypeTableName($ctype_name, '_fields');

        $this->useCache('content.fields.' . $ctype_name);

        $name = $this->getTranslatedFieldName('fieldset', $table_name);

        if (!$this->order_by) {
            $this->orderBy($name);
        }

        $this->groupBy($name);

        $this->selectOnly($name, 'fieldset');

        return $this->get($table_name, function ($item, $model) {

            if (!$item['fieldset']) {
                return false;
            }

            return $item['fieldset'];

        }, false) ?: [];
    }

    /**
     * Сохраняет порядок полей
     *
     * @param string $ctype_name Имя типа контента
     * @param array $fields_ids_list Массив id записей в нужном порядке
     * @return bool
     */
    public function reorderContentFields(string $ctype_name, $fields_ids_list) {

        // Очищаем кэш
        $this->cleanFieldCache($ctype_name);

        return $this->reorderByList($this->getContentTypeTableName($ctype_name, '_fields'), $fields_ids_list);
    }

    /**
     * Меняет флаг видимости поля
     *
     * @param string $ctype_name Имя типа контента
     * @param int $id ID поля
     * @param string $visibility_field Поле, для которого меняем флаг
     * @param int $is_visible Флаг: 0 или 1
     * @return bool
     */
    public function toggleContentFieldVisibility(string $ctype_name, $id, $visibility_field, $is_visible) {

        // Очищаем кэш
        $this->cleanFieldCache($ctype_name);

        return $this->update($this->getContentTypeTableName($ctype_name, '_fields'), $id, [
            $visibility_field => $is_visible
        ]);
    }

    /**
     * Добавляет поле типа контента,
     * Создавая ячейки в БД и внося запись в таблицу полей
     *
     * @param string $ctype_name Имя типа контента
     * @param array $field Массив данных поля
     * @param bool $is_virtual Добавляемое поле виртуальное
     * @return int ID добавленного поля
     */
    public function addContentField(string $ctype_name, array $field, $is_virtual = false) {

        // Таблица с полями
        $fields_table_name = $this->getContentTypeTableName($ctype_name, '_fields');

        // Порядок поля
        $field['ordering'] = $this->getNextOrdering($fields_table_name);

        // Присваиваем группу
        $this->makeFieldFieldset($field, $fields_table_name);

        // Записываем в базу
        $field['id'] = $this->insert($fields_table_name, $field);

        // Очищаем кэш
        $this->cleanFieldCache($ctype_name);

        // Поле не виртуальное - создаём ячейку в таблице
        if (!$is_virtual) {
            $this->alterContentField($ctype_name, $field);
        }

        cmsEventsManager::hook('ctype_field_after_add', [$field, $ctype_name, $this]);

        return $field['id'];
    }

    /**
     * Создаёт поле в таблице типа контента
     *
     * @param string $ctype_name Имя типа контента
     * @param type $field Массив данных поля
     * @return void
     */
    public function alterContentField(string $ctype_name, $field) {

        // Таблица, где будет создана ячейка поля
        $content_table_name = $this->getContentTypeTableName($ctype_name);

        $field_class  = 'field' . \string_to_camel('_', $field['type']);
        $field_parser = new $field_class(null, (isset($field['options']) ? ['options' => $field['options']] : null));

        $this->db->addTableField($content_table_name, $field['name'], $field_parser->getSQL());

        $field_parser->hookAfterAdd($content_table_name, $field, $this);

        if ($field_parser->is_denormalization) {

            $cfield_name = $field['name'] . cmsFormField::FIELD_CACHE_POSTFIX;

            $this->db->addTableField($content_table_name, $cfield_name, $field_parser->getCacheSQL());
        }

        // если есть опция полнотекстового поиска
        if (!empty($field['options']['in_fulltext_search'])) {

            $this->createFullTextIndex($ctype_name);

        } else if (!empty($field['is_in_filter']) && $field_parser->allow_index) {

            $this->db->addIndex($content_table_name, $field['name']);
        }

        return;
    }

    /**
     * Обновляет поле в таблице типа контента
     *
     * @param string $ctype_name Имя типа контента
     * @param array $field Новый массив данных поля
     * @param array $field_old Старый массив данных поля
     * @return void
     */
    public function alterUpdatedContentField(string $ctype_name, $field, $field_old) {

        if (empty($field['options'])) {
            $field['options'] = [];
        }

        // Таблица, где расположена ячейка поля
        $content_table_name = $this->getContentTypeTableName($ctype_name);

        $field_class   = 'field'.\string_to_camel('_', $field['type']);
        $field_handler = new $field_class(null, ['options' => $field['options']]);

        $field_handler->hookAfterUpdate($content_table_name, $field, $field_old, $this);

        // Если новое поле виртуальное
        if ($field_handler->is_virtual) {

            // Если поле виртуальное, а предыдущее нет, удаляем из БД
            if (!$field_old['parser']->is_virtual) {
                $this->db->dropTableField($content_table_name, $field_old['name']);
            }

            return;
        }

        // Если старое поле виртуальное, а новое нет
        if ($field_old['parser']->is_virtual && !$field_handler->is_virtual) {

            // Создаём поле
            $this->alterContentField($ctype_name, $field);

            return;
        }

        $new_lenght = $field['options']['max_length'] ?? false;
        $old_lenght = $field_old['options']['max_length'] ?? false;

        $is_change_name = $field_old['name'] !== $field['name'];
        $is_change_type = $field_old['type'] !== $field['type'];
        $is_change_len  = $new_lenght != $old_lenght;

        // Изменились данные, требующие обновления в БД
        if ($is_change_name || $is_change_type || $is_change_len) {

            if ($is_change_type || $is_change_name) {
                // Удаляем поле из всех индексов, чтобы изменение имени/типа поля прошло нормально
                $this->db->dropFieldFromIndex($content_table_name, $field_old['name']);
            }

            // Основной запрос изменения поля
            $sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$field_old['name']}` `{$field['name']}` {$field_handler->getSQL()}";
            // Пробуем сменить
            $result = $this->db->query($sql, false, true);
            // Не получилось конвертировать (вероятно задан sql_mode в MySQL)
            if ($result === false) {
                // очищаем данные
                $this->db->query("UPDATE `{#}{$content_table_name}` SET `{$field_old['name']}` = NULL");
                // И заново меняем
                $this->db->query($sql);
            }

            // Работа с денормализацией
            if (($is_change_type || $is_change_name) &&
                    ($field_old['parser']->is_denormalization || $field_handler->is_denormalization)) {

                // поля денормализации
                $old_cfield_name = $field_old['name'] . cmsFormField::FIELD_CACHE_POSTFIX;
                $new_cfield_name = $field['name'] . cmsFormField::FIELD_CACHE_POSTFIX;

                $update_cache_sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$old_cfield_name}` `{$new_cfield_name}` {$field_handler->getCacheSQL()}";

                // Оба поля
                if ($field_old['parser']->is_denormalization && $field_handler->is_denormalization) {

                    $this->db->query($update_cache_sql);

                // Новое без поддержки денормализации
                } elseif ($field_old['parser']->is_denormalization && !$field_handler->is_denormalization) {

                    $this->db->dropTableField($content_table_name, $old_cfield_name);

                // Новое с поддержкой денормализации
                } elseif (!$field_old['parser']->is_denormalization && $field_handler->is_denormalization) {

                    $this->db->addTableField($content_table_name, $new_cfield_name, $field_handler->getCacheSQL());
                }
            }

            // имя поля сменилось, меняем в наборах
            if ($is_change_name) {
                if (!$this->table_prefix) {
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

        if ($field_handler->allow_index) {

            if ($field['is_in_filter']) {
                $this->db->addIndex($content_table_name, $field['name']);
            } else {
                $this->db->dropIndex($content_table_name, $field_old['name']);
            }

        } else {
            $this->db->dropIndex($content_table_name, $field_old['name']);
        }

        // если есть опция полнотекстового поиска и ее значение изменилось
        if (array_key_exists('in_fulltext_search', $field['options'])) {

            $old_in_fulltext_search = $field_old['options']['in_fulltext_search'] ?? false;

            if ($field['options']['in_fulltext_search'] != $old_in_fulltext_search) {

                // Выключена опция
                if (!$field['options']['in_fulltext_search']) {

                    $this->db->dropFieldFromIndex($content_table_name, $field_old['name'], 'FULLTEXT');

                } else {

                    $this->createFullTextIndex($ctype_name, $field['name']);
                }
            }
        }

    }

    /**
     * Сохраняет поле типа контента,
     * Обновляя ячейки в БД и запись в таблице полей
     *
     * @param string $ctype_name Имя типа контента
     * @param int $id id поля
     * @param array $field Массив данных поля
     * @return bool
     */
    public function updateContentField(string $ctype_name, $id, $field) {

        // Таблица с полями
        $fields_table_name = $this->getContentTypeTableName($ctype_name, '_fields');

        // Текущие данные поля
        $field_old = $this->getContentField($ctype_name, $id);

        // Системные поля не трогаем
        if (!$field_old['is_system']) {

            // Обновляем ячейки в БД
            $this->alterUpdatedContentField($ctype_name, $field, $field_old);
        }

        // Присваиваем группу
        $this->makeFieldFieldset($field, $fields_table_name);

        $result = $this->update($fields_table_name, $id, $field);

        if ($result) {

            $field['id'] = $id;

            cmsEventsManager::hook('ctype_field_after_update', [$field, $ctype_name, $this]);
            cmsEventsManager::hook('ctype_field_' . str_replace(['{', '}'], '', $ctype_name) . '_after_update', [$field, $this]);
        }

        // Очищаем кэш
        $this->cleanFieldCache($ctype_name);

        return $result;
    }

    /**
     * Удаляет поле типа контента
     *
     * @param string|int $ctype_name_or_id Имя или id типа контента
     * @param int $id id поля
     * @param string $by_field По какой ячейке ищем запись: id или name
     * @param bool $is_forced Удалять защищённые поля (is_fixed)
     * @return bool
     */
    public function deleteContentField($ctype_name_or_id, $id, $by_field = 'id', $is_forced = false) {

        if (is_numeric($ctype_name_or_id)) {

            $ctype = $this->getContentType($ctype_name_or_id);

            if (!$ctype) {
                return false;
            }

            $ctype_name = $ctype['name'];

        } else {
            $ctype_name = $ctype_name_or_id;
        }

        $field = $this->getContentField($ctype_name, $id, $by_field);

        // Фиксированные поля не удаляем
        if ($field['is_fixed'] && !$is_forced) {
            return false;
        }

        cmsEventsManager::hook('ctype_field_before_delete', [$field, $ctype_name, $this]);

        // Таблица, где будет создана ячейка поля
        $content_table_name = $this->getContentTypeTableName($ctype_name);
        // Таблица с полями
        $fields_table_name  = $this->getContentTypeTableName($ctype_name, '_fields');

        // Удаляем запись о поле
        $this->delete($fields_table_name, $id, $by_field);
        // Обновляем порядок полей
        $this->reorder($fields_table_name);

        // Очищаем кэш
        $this->cleanFieldCache($ctype_name);

        // Для невиртуальных полей удаляем ячейку в таблице записей типа контента
        if (!$field['parser']->is_virtual) {
            $this->db->dropTableField($content_table_name, $field['name']);
        }

        $field['parser']->hookAfterRemove($content_table_name, $field, $this);

        // Для полей с денормализацией удаляем нужную ячейку
        if ($field['parser']->is_denormalization) {
            $this->db->dropTableField($content_table_name, $field['parser']->getDenormalName());
        }

        return true;
    }

    /**
     * Создает fulltext индекс согласно настроек полей типа контента
     *
     * @param string $ctype_name Имя типа контента
     * @param string|null $add_field Название поля, для которого принудительно нужно создать индекс
     * @return bool
     */
    protected function createFullTextIndex(string $ctype_name, $add_field = null) {

        // Идекс может быть только один
        $index_name = 'fulltext_search';

        $index_fields = [];

        // важен порядок индексов, поэтому создаем их так, как они будут в запросе
        // для этого получаем все поля этого типа контента
        $fields = $this->getContentFields($ctype_name);

        foreach ($fields as $field) {

            $is_text = $field['handler']->getOption('in_fulltext_search') || $field['name'] === $add_field;
            if (!$is_text) {
                continue;
            }

            $index_fields[] = $field['name'];
        }

        if ($index_fields) {

            return $this->db->addIndex($this->getContentTypeTableName($ctype_name), $index_fields, $index_name, 'FULLTEXT', true);
        }

        return false;
    }

    /**
     * Очиста кэша
     *
     * @param string $ctype_name Имя типа контента
     */
    protected function cleanFieldCache(string $ctype_name) {

        cmsCache::getInstance()->clean('content.fields.'.$ctype_name);
        cmsCache::getInstance()->clean('content.list.'.$ctype_name);
        cmsCache::getInstance()->clean('content.item.'.$ctype_name);

    }

    /**
     * Присваивает группу поля
     *
     * @param array $field Массив данных поля
     * @param string $fields_table_name Таблица с полями
     */
    protected function makeFieldFieldset(array &$field, string $fields_table_name) {

        // Если не выбрана группа, обнуляем поля групп
        foreach ($field as $key => $value) {
            if (strpos($key, 'fieldset') === 0 && !$value) {
                $field[$key] = null;
            }
        }

        // если создается новая группа, то выбираем ее
        if (!empty($field['new_fieldset'])) {
            $field[$this->getTranslatedFieldName('fieldset', $fields_table_name)] = $field['new_fieldset'];
        }

    }

}
