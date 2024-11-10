<?php
/**
 * @property \cmsModel $model
 */
class actionCspReport extends cmsAction {

    public function run() {

        if (empty($this->options['enable_csp']) ||
                empty($this->options['enable_report']) ||
                empty($this->options['csp_str'])) {
            return cmsCore::error404();
        }

        $body = $this->request->getContent();

        if (!$body) {
            return cmsCore::error404();
        }

        $csp_report = json_decode($body, true);

        if (!$csp_report || empty($csp_report['csp-report'])) {
            return cmsCore::error404();
        }

        $csp_report_fields = [
            'document-uri',
            'referrer',
            'violated-directive',
            'effective-directive',
            'blocked-uri',
            'line-number',
            'status-code'
        ];

        $data = [];

        foreach ($csp_report_fields as $name) {

            if (isset($csp_report['csp-report'][$name]) && is_array($csp_report['csp-report'][$name])) {
                return cmsCore::error404();
            }

            if (!empty($csp_report['csp-report'][$name])) {
                $data[str_replace('-', '_', $name)] = strip_tags((string)$csp_report['csp-report'][$name]);
            }
        }

        if (!$data) {
            return cmsCore::error404();
        }

        $data['ip'] = function ($db) {
            return '\''.$db->escape(string_iptobin($this->cms_user->ip)).'\'';
        };

        $this->model->insert('csp_logs', $data);

        return $this->cms_core->response->setStatusCode(204)->sendAndExit();
    }

}
