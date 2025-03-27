<?php

class onCspEngineStart extends cmsAction {

    public function run() {

        if (empty($this->options['enable_csp']) || empty($this->options['csp_str'])) {
            return true;
        }

        $header_name = 'Content-Security-Policy';

        if (!empty($this->options['is_report_only']) && !empty($this->options['enable_report'])) {
            $header_name = 'Content-Security-Policy-Report-Only';
        }

        $list = [str_replace('{nonce}', cmsResponse::getNonce(), $this->options['csp_str'])];

        if (!empty($this->options['enable_report'])) {

            $report_uri = href_to_abs('csp', 'report');
            $endpoint_name = 'icms-csp-ep';

            // Директива устаревшая, но пока используем
            $list[] = 'report-uri ' . $report_uri;

            $this->cms_core->response->setHeader('Reporting-Endpoints', $endpoint_name.'="'.$report_uri.'"');

            // Если для HTTP включить, то отчёты
            // не будут отсылаться, даже при заданной report-uri
            if ($this->cms_core->request->isSecure()) {
                $list[] = 'report-to '.$endpoint_name;
            }
        }

        $this->cms_core->response->setHeader($header_name, implode('; ', $list));

        return true;
    }

}
