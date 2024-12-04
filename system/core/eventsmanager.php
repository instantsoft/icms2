<?php

/**
 * Класс управления событиями
 * @doc https://docs.instantcms.ru/dev/controllers/hooks
 */
class cmsEventsManager {

    /**
     * Список всех слушателей и событий
     * @var array
     */
    private static $structure = null;

    /**
     * Оповещает слушателей о произошедшем событии
     * Входящие данные $data передаются каждому слушателю по очереди,
     * на выходе возвращается измененный слушателями параметр $data
     *
     * @param array|string $event_name Название события/массив событий
     * @param mixed $data Параметр события
     * @param ?mixed $default_return Значение, возвращаемое по умолчанию если у события нет слушателей
     * @param ?cmsRequest $_request Объект запроса
     * @return mixed Обработанные данные
     */
    public static function hook($event_name, $data = false, $default_return = null, $_request = null) {

        // Используйте массив событий, если они с разным названиями,
        // но с одинаковыми параметрами
        if (is_array($event_name)) {

            foreach ($event_name as $_event_name) {
                $data = self::hook($_event_name, $data, $default_return, $_request);
            }

            return $data;
        }

        //получаем все активные контроллеры, привязанные к указанному событию
        $listeners = self::getEventListeners($event_name);

        //если активных контроллеров нет, возвращаем данные без изменений
        if (!$listeners) {

            cmsDebugging::pointProcess('events_empty', [
                'data' => 'hook => ' . $event_name,
                'context' => [
                    'target' => null,
                    'subject' => $event_name
                ]
            ], 1);

            return is_null($default_return) ? $data : $default_return;
        }

        $request = $_request ?? new cmsRequest([], cmsRequest::CTX_INTERNAL);

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach ($listeners as $listener) {

            $result = self::runHook($listener, $event_name, $data, $request);

            if ($result !== null) {
                $data = $result;
            }
        }

        return $data;
    }

    /**
     * Оповещает слушателей о произошедшем событии
     * Входящие данные $data передаются каждому слушателю в изначальном виде,
     * на выходе возвращается массив с ответами от каждого слушателя
     *
     * @param string $event_name Название события
     * @param mixed $data Параметр события
     * @param mixed $default_return Значение, возвращаемое по умолчанию если у события нет слушателей
     * @param ?cmsRequest $_request Объект запроса
     * @return array Обработанный массив данных
     */
    public static function hookAll($event_name, $data = false, $default_return = null, $_request = null) {

        //получаем все активные контроллеры, привязанные к указанному событию
        $listeners = self::getEventListeners($event_name);

        //если активных контроллеров нет, возвращаем данные без изменений
        if (!$listeners) {

            cmsDebugging::pointProcess('events_empty', [
                'data' => 'hookAll => ' . $event_name,
                'context' => [
                    'target' => null,
                    'subject' => $event_name
                ]
            ], 1);

            return is_null($default_return) ? false : $default_return;
        }

        $results = [];

        $request = $_request ?? new cmsRequest([], cmsRequest::CTX_INTERNAL);

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach ($listeners as $listener) {

            $result = self::runHook($listener, $event_name, $data, $request, 'hookAll');

            if ($result !== false && $result !== null) {
                $results[$listener] = $result;
            }
        }

        return $results;
    }

    /**
     * Запускает слушателя
     *
     * @param string $listener Имя контроллера
     * @param string $event_name Название события
     * @param mixed $data Параметр события
     * @param ?cmsRequest $request Объект запроса
     * @param string $debug_type Отладочный тип
     * @return mixed
     */
    public static function runHook($listener, $event_name, $data = false, $request = null, $debug_type = 'hook') {

        if (!cmsController::enabled($listener)) {
            return null;
        }

        $controller = cmsCore::getController($listener, $request, false);

        if (!$controller || ($controller->mb_installed && !$controller->isControllerInstalled($listener))) {
            return null;
        }

        cmsDebugging::pointStart('events');

        $data = $controller->runHook($event_name, [$data]);

        cmsDebugging::pointProcess('events', [
            'data' => $debug_type . ' :: ' . $listener . ' => ' . $event_name,
            'context' => [
                'target' => $listener,
                'subject' => $event_name
            ]
        ], 1);

        return $data;
    }

    /**
     * Возвращает список всех слушателей указанного события
     * @param string $event_name Название события
     * @return array Список слушателей
     */
    public static function getEventListeners(string $event_name) {

        if (self::$structure === null) {
            self::$structure = self::getAllListeners();
        }

        return self::$structure[$event_name] ?? [];
    }

    /**
     * Возвращает список всех слушателей для всех событий
     * @return array
     */
    public static function getAllListeners() {

        $cache     = cmsCache::getInstance();
        $cache_key = 'events';

        if (false !== ($structure = $cache->get($cache_key))) {
            return $structure;
        }

        $events = cmsCore::getControllersEvents();
        if (!$events) {
            return [];
        }

        $structure = [];

        foreach ($events as $controller_name => $hooks) {
            foreach ($hooks as $ordering => $event_name) {
                $structure[$event_name][$ordering] = $controller_name;
            }
        }

        foreach ($structure as $event_name => $controllers) {
            ksort($structure[$event_name]);
        }

        $cache->set($cache_key, $structure, 86400);

        return $structure;
    }

}
