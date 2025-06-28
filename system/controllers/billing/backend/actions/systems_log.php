<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingSystemsLog extends cmsAction {

    public function run($id) {

        if (!$id) {
            return cmsCore::error404();
        }

        $system = $this->model->getPaymentSystem($id);
        if (!$system) {
            return cmsCore::error404();
        }

        $log_text = '';
        $line_count = 100;

        $log_file = $this->cms_config->cache_path . 'billing/' . $system['name'] . '_pay_api.log';

        if (is_readable($log_file)) {
            $log_text = $this->tail($log_file, $line_count);
        }

        return $this->cms_template->render([
            'log_path'   => str_replace($this->cms_config->root_path, $this->cms_config->root, $log_file),
            'line_count' => $line_count,
            'system'     => $system,
            'log_text'   => $log_text
        ]);
    }

    private function tail($filename, $lines = 100) {

        $f = fopen($filename, 'rb');
        if (!$f) { return ''; }

        fseek($f, 0, SEEK_END);

        $buffer     = '';
        $chunk_size = 4096;
        $pos        = ftell($f);
        $line_count = 0;

        while ($pos > 0 && $line_count <= $lines) {

            $read_size = min($chunk_size, $pos);

            $pos -= $read_size;

            fseek($f, $pos);
            $chunk = fread($f, $read_size);

            $buffer = $chunk . $buffer;

            $line_count = substr_count($buffer, "\n");
        }

        fclose($f);

        $lines_array = explode("\n", trim($buffer));

        return implode("\n", array_slice($lines_array, -$lines));
    }

}
