<?php

class onAdminCronOptimizeTables extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $result = $this->model->db->query('show tables');

        $tables = [];

        while ($data = $this->model->db->fetchRow($result)) {
            $tables[] = $data[0];
        }

        foreach($tables as $table_name) {
            $this->model->db->query("OPTIMIZE TABLE `{$table_name}`", false, true);
        }

        return true;
    }

}
