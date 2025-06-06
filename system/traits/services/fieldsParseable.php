<?php

namespace icms\traits\services;

/**
 * Трейт контроллеров для формирования результатов
 * Выполнения методов parse, afterParse и getStringValue
 * Трейт использует контекст контроллеров/экшенов/виджетов
 */
trait fieldsParseable {

    /**
     * Формирует HTML поля и строковое значение, дополняя им массив $fields
     *
     * @param array $fields Массив полей
     * @param array $item Массив записи
     * @return array
     */
    public function parseContentFields(array $fields, array $item) {

        // Запоминаем копию записи для заполнения отпарсенных полей
        $item_parsed = $item;

        // Парсим значения полей
        foreach ($fields as $name => $field) {
            $item_parsed[$name] = $fields[$name]['html'] = $field['handler']->setItem($item)->parse($item[$name] ?? '');
        }

        // Для каких необходимо, обрабатываем дополнительно
        foreach ($fields as $name => $field) {
            $fields[$name]['string_value'] = $field['handler']->setItem($item_parsed)->getStringValue($item[$name] ?? '');
            $fields[$name]['html'] = $field['handler']->afterParse($fields[$name]['html'], $item_parsed);
            $fields[$name]['title'] = $field['handler']->getTitle();
        }

        return $fields;
    }

    /**
     * Формирует доступные поля для показа в записи
     *
     * @param array $fields Массив полей
     * @param array $item Массив записи
     * @param string $user_id_field_name Название поля с id пользователя
     * @param ?callable $callback Колбэк
     * @return array
     */
    public function getViewableItemFields(array $fields, array $item, string $user_id_field_name = 'user_id', $callback = null) {

        $item_fields = [];

        foreach ($fields as $field) {

            if (is_callable($callback)) {
                if (!$callback($field, $item)) {
                    continue;
                }
            }

            // Пропускаем поля, которые не для вывода в записи,
            if (!$field['is_in_item']) {
                continue;
            }

            // Позиция поля "На позиции в специальном виджете"
            if (!empty($field['options']['is_in_item_pos']) && !in_array('page', $field['options']['is_in_item_pos'])) {
                continue;
            }

            if (\is_empty_value($field['html'])) {
                continue;
            }

            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) {

                // если группа пользователя не имеет доступ к чтению этого поля,
                // проверяем на доступ к нему для авторов

                if (empty($item[$user_id_field_name]) || empty($field['options']['author_access'])) {
                    continue;
                }

                if (!in_array('is_read', $field['options']['author_access'], true)) {
                    continue;
                }

                if ($item[$user_id_field_name] != $this->cms_user->id) {
                    continue;
                }
            }

            $item_fields[$field['name']] = $field;
        }

        return $item_fields;
    }

    /**
     * Применяет хуки полей после всех операций
     *
     * @param array $fields Массив полей
     * @param array $item Массив записи
     * @return array
     */
    public function applyFieldHooksToItem(array $fields, array $item) {

        foreach($fields as $field){
            $item = $field['handler']->hookItem($item, $fields);
        }

        return $item;
    }

}
