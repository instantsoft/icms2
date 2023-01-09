<?php

namespace icms\traits\controllers\models;

/**
 * Трейт для поддержки транзакций моделей
 *
 */
trait transactable {

    /**
     * Транзакция началась
     * @var boolean
     */
    public $is_transaction_started = false;

    public function processTransaction($payload_callback, $after_autocommit_on = false) {

        // нам не нужно, чтобы внутри транзакции при ошибке запроса
        // всё умирало
        $this->db->query_quiet = true;

        // флаг результата выполнения
        $success = true;

        // мы внутри транзакции?
        $is_autocommit_on = $this->db->isAutocommitOn();

        // выключаем автокоммит, чтобы все запросы были в транзакции
        // если автокоммит выключен, то мы уже в транзакции
        if ($is_autocommit_on) {
            $this->db->autocommitOff();
        }

        try {

            $success = call_user_func_array($payload_callback, [$this]);

        } catch (Exception $e) {

            error_log($e->getMessage());

            $success = false;
        }

        $this->db->query_quiet = null;

        if ($is_autocommit_on || $after_autocommit_on) {

            $this->endTransaction($success);
        }

        return $success;
    }

    public function startTransaction() {

        $this->is_transaction_started = true;

        $this->db->autocommitOff();

        return $this;
    }

    public function endTransaction($success) {

        if ($success) {

            $this->db->commit();

        } else {

            $this->db->rollback();

        }

        $this->db->autocommitOn();

        $this->is_transaction_started = false;

        return $this;
    }

    public function forUpdate() {
        return $this->setReadType('FOR UPDATE');
    }

    public function lockInShareMode() {
        return $this->setReadType('LOCK IN SHARE MODE');
    }

    public function setTransactionIsolationLevel($level) {
        $this->db->query("SET TRANSACTION ISOLATION LEVEL {$level};"); return $this;
    }

}
