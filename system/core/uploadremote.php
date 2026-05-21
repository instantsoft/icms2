<?php

class cmsUploadremote {

    /**
     * URL для скачивания
     *
     * @var string
     */
    private $url;

    /**
     * Максимальный размер файла
     *
     * @var int
     */
    private $max_size = 0;

    /**
     * Максимальное число redirect
     *
     * @var int
     */
    private $max_redirects = 4;

    /**
     * Текущее число redirect
     *
     * @var int
     */
    private $redirects = 0;

    /**
     * Разрешённые хосты
     *
     * @var array
     */
    private $allowed_hosts = [];

    /**
     * HTTP-коды redirect
     */
    const REDIRECT_CODES = [
        301, 302, 303, 307, 308
    ];

    public function __construct(
        string $url,
        int    $max_size = 0,
        array  $allowed_hosts = [],
        int    $max_redirects = 4
    ) {

        $this->url = $url;

        $this->max_size = max(0, $max_size);

        $this->max_redirects = max(1, $max_redirects);

        $this->allowed_hosts = array_map(
            'strtolower',
            $allowed_hosts
        );
    }

    /**
     * Скачивает файл
     *
     * @return array
     */
    public function download() {

        $url = $this->url;

        while (true) {

            $url_data = $this->validateUrl($url);

            if (!$url_data) {
                return $this->error('Invalid URL');
            }

            $host = $url_data['host'];
            $port = $url_data['port'];

            if (
                $this->allowed_hosts &&
                !in_array($host, $this->allowed_hosts, true)
            ) {
                return $this->error('Error Remote Host');
            }

            $ips = $this->resolveHost($host);

            if (!$ips) {
                return $this->error('DNS resolve failed');
            }

            foreach ($ips as $ip) {
                if (!$this->isAllowedIp($ip)) {
                    return $this->error('Forbidden IP');
                }
            }

            $headers         = [];
            $body            = '';
            $downloaded_size = 0;

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_REDIR_PROTOCOLS =>  CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_HEADER => false,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT => 'InstantCMS/2.0',
                CURLOPT_RESOLVE => $this->buildCurlResolve($host, $port, $ips),
                CURLOPT_HEADERFUNCTION => static function ($curl, $header) use (&$headers) {

                    $length = strlen($header);
                    $header = trim($header);

                    if ($header === '') {
                        return $length;
                    }

                    if (strpos($header, ':') === false) {
                        return $length;
                    }

                    [$name, $value] = explode(':', $header, 2);

                    $name = strtolower(trim($name));
                    $value = trim($value);

                    if (!isset($headers[$name])) {
                        $headers[$name] = [];
                    }

                    $headers[$name][] = $value;

                    return $length;
                },
                CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use (&$body, &$downloaded_size) {

                    $chunk_length = strlen($chunk);
                    $downloaded_size += $chunk_length;

                    if (
                        $this->max_size > 0 &&
                        $downloaded_size > $this->max_size
                    ) {
                        return 0;
                    }

                    $body .= $chunk;

                    return $chunk_length;
                }
            ]);

            $result = curl_exec($curl);

            $curl_error = curl_error($curl);

            $http_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($result === false) {

                if (
                    $this->max_size > 0 &&
                    $downloaded_size > $this->max_size
                ) {
                    return $this->error('File too large');
                }

                return $this->error('Download failed: ' . $curl_error);
            }

            if (in_array($http_code, self::REDIRECT_CODES, true)) {

                $location = trim($headers['location'][0] ?? '');

                if (!$location) {
                    return $this->error('Broken redirect');
                }

                $this->redirects++;

                if ($this->redirects > $this->max_redirects) {
                    return $this->error('Too many redirects');
                }

                $url = $this->buildRedirectUrl($url, $location);

                continue;
            }

            if ($http_code >= 400) {
                return $this->error('HTTP error: ' . $http_code);
            }

            if (!$body) {
                return $this->error(LANG_UPLOAD_ERR_NO_FILE);
            }

            $file_name = $this->extractFileName($url, $headers);

            return [
                'success'  => true,
                'file_bin' => $body,
                'name'     => files_sanitize_name($file_name)
            ];
        }
    }

    /**
     * Валидация URL
     *
     * @param string $url
     * @return bool|array
     */
    private function validateUrl(string $url) {
        // Требуем path в URL,
        // т.к. downloader не должен работать
        // с root/query-only URL
        if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            return false;
        }

        $data = parse_url($url);

        if (
            !$data ||
            empty($data['scheme']) ||
            empty($data['host'])
        ) {
            return false;
        }

        $scheme = strtolower($data['scheme']);

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($data['host']);

        // Запрещаем URL по IP
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return false;
        }

        $port = (int)(
            $data['port'] ??
            ($scheme === 'https' ? 443 : 80)
        );

        // Только стандартные порты
        if (!in_array($port, [80, 443], true)) {
            return false;
        }

        return [
            'scheme' => $scheme,
            'host'   => $host,
            'port'   => $port
        ];
    }

    /**
     * DNS resolve
     *
     * @param string $host
     * @return array
     */
    private function resolveHost(string $host) {

        $records = dns_get_record($host, DNS_A + DNS_AAAA);

        if (!$records) {
            return [];
        }

        $ips = [];

        foreach ($records as $record) {

            if (!empty($record['ip'])) {
                $ips[] = $record['ip'];
            }

            if (!empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        return array_values(array_unique($ips));
    }

    /**
     * Проверка IP
     *
     * @param string $ip
     * @return bool
     */
    private function isAllowedIp(string $ip) {

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE |
            FILTER_FLAG_NO_RES_RANGE
        ) === $ip;
    }

    /**
     * Защита от DNS rebinding
     *
     * @param string $host
     * @param int $port
     * @param array $ips
     * @return array
     */
    private function buildCurlResolve(string $host, int $port, array $ips) {

        $resolve = [];

        foreach ($ips as $ip) {

            // IPv6
            if (strpos($ip, ':') !== false) {
                $ip = '[' . $ip . ']';
            }

            $resolve[] = $host . ':' . $port . ':' . $ip;
        }

        return $resolve;
    }

    /**
     * Построение redirect URL
     *
     * @param string $base_url
     * @param string $location
     * @return string
     */
    private function buildRedirectUrl(string $base_url, string $location) {

        // полный url
        if (
            strpos($location, 'http://') === 0 ||
            strpos($location, 'https://') === 0
        ) {
            return $location;
        }

        $base = parse_url($base_url);

        $scheme = $base['scheme'];
        $host   = $base['host'];

        $port = isset($base['port']) ? ':' . $base['port'] : '';

        // относительный
        if (strpos($location, '//') === 0) {
            return $scheme . ':' . $location;
        }

        // только абсолютный путь
        if (strpos($location, '/') === 0) {
            return $scheme . '://' . $host . $port . files_normalize_path($location);
        }

        // относительный путь
        $base_path = $base['path'] ?? '/';

        $dir = dirname($base_path);

        $path = $dir . '/' . $location;

        return $scheme . '://' . $host . $port . '/' . files_normalize_path($path);
    }

    /**
     * Возвращает имя файла
     *
     * @param string $url
     * @param array $headers
     * @return string
     */
    private function extractFileName(string $url, array $headers) {

        $disposition = $headers['content-disposition'][0] ?? '';

        if ($disposition) {

            if (
                preg_match(
                    '/filename\*?=(?:UTF-8\'\')?["\']?([^"\';]+)["\']?/iu',
                    $disposition,
                    $matches
                )
            ) {
                return trim(rawurldecode($matches[1]));
            }
        }

        $path = parse_url($url,  PHP_URL_PATH);

        $name = basename((string)$path);

        return $name ?: 'file';
    }

    /**
     * Возврат ошибки
     *
     * @param string $message
     * @return array
     */
    private function error(string $message) {
        return [
            'success' => false,
            'error'   => $message,
            'name'    => '',
            'path'    => ''
        ];
    }

}
