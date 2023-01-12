<?php

namespace icms\traits;

/**
 * Трейт для событий
 */
trait eventDispatcher {

    protected $event_listeners = [];

    /**
     * Выполняет событие
     *
     * @param string $name Имя события
     * @param array $params
     * @return array
     */
    public function dispatchEvent($name, $params = []) {

        if(empty($this->event_listeners[$name])){
            return false;
        }

        array_unshift($params, $this);

        foreach ($this->event_listeners[$name] as $callback) {
            call_user_func_array($callback, $params);
        }

        return true;
    }

    /**
     * Добавляет слушателя
     *
     * @param string $name Имя события
     * @param callable $callback
     */
    public function addEventListener($name, $callback) {

        $this->event_listeners[$name][] = $callback;

    }

}
