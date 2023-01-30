<?php

class cmsQueue {

    protected static $max_attempts = 4;
    protected static $max_run_jobs = 50;
    protected static $table        = 'jobs';

    /**
     * Добавляет задачу в очередь
     *
     * @param string $queue Название очереди
     * @param array $data
     * @param integer $priority Приоритет, по умолчанию 1
     * @return integer
     */
    public static function pushOn($queue, $data, $priority = 1) {
        return static::pushToDatabase([
            'payload'  => $data,
            'queue'    => $queue,
            'priority' => $priority
        ]);
    }

    /**
     * Добавляет задачу в очередь с отсрочкой в секундах или по дате
     * @param integer|date $date
     * @param string $queue
     * @param array $data
     * @param integer $priority
     * @return integer
     */
    public static function pushOnLater($date, $queue, $data, $priority = 1) {
        return static::pushToDatabase([
            'payload'      => $data,
            'queue'        => $queue,
            'date_started' => date('Y-m-d H:i:s', (is_numeric($date) ? (time() + $date) : strtotime($date))),
            'priority'     => $priority
        ]);
    }

    public static function getMaxAttempts() {
        return static::$max_attempts;
    }

    public static function getTableName() {
        return static::$table;
    }

    public static function setTableName($name) {
        static::$table = $name;
    }

    protected static function pushToDatabase($data) {

        $model = new cmsModel();

        $data['payload'] = json_encode($data['payload']);

        return $model->insert(static::$table, $data);
    }

    public static function runJobs($queue = null) {

        $model = new cmsModel();

        $model->orderByList([
            ['by' => 'date_started', 'to' => 'asc'],
            ['by' => 'priority', 'to' => 'desc'],
            ['by' => 'date_created', 'to' => 'asc']
        ]);

        $model->limit(static::$max_run_jobs);

        if ($queue) {
            $model->filterEqual('queue', $queue);
        }

        $json_decode_errors = [];

        $jobs = $model->filterIsNull('is_locked')->
                    filterLtEqual('attempts', static::$max_attempts)->
                    filterStart()->
                    filterIsNull('date_started')->
                    filterOr()->
                    filterLtEqual('date_started', date('Y-m-d H:i:s'))->
                    filterEnd()->get(static::$table, function ($item, $model) use(&$json_decode_errors) {

            $item['payload'] = json_decode($item['payload'], true);

            if($item['payload'] === null){

                $json_decode_errors[$item['id']] = json_last_error_msg();

                return false;

            } else if (!isset($item['payload']['params'])) {

                $item['payload']['params'] = [];
            }

            array_unshift($item['payload']['params'], ($item['attempts'] + 1));

            return $item;
        });

        if (!$jobs) {
            return false;
        }

        // помечаем полученные задания как запущенные
        $model->filterIn('id', array_keys($jobs))->updateFiltered(static::$table, [
            'is_locked'    => 1,
            'date_started' => '',
            'attempts'     => function ($db) {
                return '`attempts` + 1';
            }
        ], true);

        // Если ошибки JSON
        foreach ($json_decode_errors as $id => $json_last_error_msg) {
            static::setJobError($jobs[$id], $json_last_error_msg);
        }

        foreach ($jobs as $job) {

            $result = static::runJob($job);

            // если задание выполнено успешно, удаляем его
            if ($result === true) {
                static::deleteJob($job);
            } else
            // в случае если передали false, то нам нужен повторный запуск
            if ($result === false) {
                static::unlockJob($job);
            }
            // иначе пишем ошибку, неразблокируя задачу
            else {
                static::setJobError($job, $result);
            }
        }

        return true;
    }

    public static function runJob($job) {

        $controller = cmsCore::getController($job['payload']['controller']);

        try {

            $result = true;

            if (isset($job['payload']['hook'])) {
                $result = $controller->runHook($job['payload']['hook'], $job['payload']['params']);
            }

            if (isset($job['payload']['action'])) {
                $result = $controller->runAction($job['payload']['action'], $job['payload']['params']);
            }
        } catch (Exception $e) {

            $result = $e->getMessage();
        }

        return $result;
    }

    public static function deleteJob($job) {

        $model = new cmsModel();

        return $model->delete(static::$table, $job['id']);
    }

    public static function setJobError($job, $error_text) {

        $model = new cmsModel();

        return $model->update(static::$table, $job['id'], ['last_error' => $error_text], true);
    }

    public static function unlockJob($job) {

        $model = new cmsModel();

        return $model->update(static::$table, $job['id'], ['is_locked' => null], true);
    }

    public static function restartJob($job) {

        $model = new cmsModel();

        return $model->update(static::$table, $job['id'], ['is_locked' => null, 'last_error' => null, 'attempts' => 0], true);
    }

}
