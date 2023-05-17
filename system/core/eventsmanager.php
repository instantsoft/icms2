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
     * @param cmsRequest|false $_request Объект запроса
     * @return array Обработанный массив данных
     */
    public static function hook($event_name, $data = false, $default_return = null, $_request = false) {

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

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach ($listeners as $listener) {

            $request = ($_request === false) ? new cmsRequest([], cmsRequest::CTX_INTERNAL) : $_request;

            $controller = cmsCore::getController($listener, $request);

            if ($controller->mb_installed && !$controller->isControllerInstalled($listener)) {
                unset($controller);
                continue;
            }

            cmsDebugging::pointStart('events');

            $data = $controller->runHook($event_name, [$data]);

            cmsDebugging::pointProcess('events', [
                'data' => 'hook :: ' . $listener . ' => ' . $event_name,
                'context' => [
                    'target' => $listener,
                    'subject' => $event_name
                ]
            ], 1);

            unset($controller);
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
     * @param object $_request Объект запроса
     * @return array Обработанный массив данных
     */
    public static function hookAll($event_name, $data = false, $default_return = null, $_request = false) {

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

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach ($listeners as $listener) {

            $request = ($_request === false) ? new cmsRequest([], cmsRequest::CTX_INTERNAL) : $_request;

            $controller = cmsCore::getController($listener, $request);

            if ($controller->mb_installed && !$controller->isControllerInstalled($listener)) {
                unset($controller);
                continue;
            }

            cmsDebugging::pointStart('events');

            $result = $controller->runHook($event_name, [$data]);

            if ($result !== false) {
                $results[$listener] = $result;
            }

            cmsDebugging::pointProcess('events', [
                'data' => 'hookAll :: ' . $listener . ' => ' . $event_name,
                'context' => [
                    'target' => $listener,
                    'subject' => $event_name
                ]
            ], 1);

            unset($controller);
        }

        return $results;
    }

    /**
     * Возвращает список всех слушателей указанного события
     * @param string $event_name Название события
     * @return array Список слушателей
     */
    public static function getEventListeners($event_name) {

        $listeners = [];

        if (self::$structure === null) {
            self::$structure = self::getAllListeners();
        }

        if (isset(self::$structure[$event_name])) {
            $listeners = self::$structure[$event_name];
        }

        return $listeners;
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

            if (!cmsController::enabled($controller_name)) {
                continue;
            }

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
