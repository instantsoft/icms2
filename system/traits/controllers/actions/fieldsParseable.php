<?php

namespace icms\traits\controllers\actions;

/**
 * Трейт контроллеров для формирования результатов
 * Выполнения методов parse, afterParse и getStringValue
 * Трейт использует контекст контроллеров/экшенов
 */
trait fieldsParseable {

    public function parseContentFields($fields, $item) {

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
        }

        return $fields;
    }

}
