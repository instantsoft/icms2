<?php
/**
 * Класс для проверки IP адреса
 */
class cmsIp {

    /**
     * Универсальный список доверенных IP (IPv4/IPv6, CIDR, диапазоны)
     * [
     *     '192.168.1.0/24',
     *     '10.0.0.5',
     *     '2001:db8::/64',
     *     '2001:db8::1 - 2001:db8::ffff'
     * ]
     * @var array
     */
    private $trusted_list = [];

    /**
     * @param array $trusted Список IP
     */
    public function __construct(array $trusted = []) {
        $this->trusted_list = $trusted;
    }

    /**
     * Проверка, находится ли IP-адрес в доверенном списке
     *
     * @param string $ip IPv4 или IPv6 адрес для проверки
     * @return bool
     */
    public function isIPTrusted(string $ip) {

        if (!$this->isValidIP($ip)) {
            return false;
        }

        foreach ($this->trusted_list as $range) {
            if ($this->ipMatchesRange($ip, $range)) {
                return true;
            }
        }

        return empty($this->trusted_list);
    }

    /**
     * Проверяет, является ли переданное значение IP адресом
     *
     * @param string $ip IPv4 или IPv6 адрес для проверки
     * @return bool
     */
    private function isValidIP(string $ip) {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Определяет, входит ли IP в указанный диапазон / CIDR / IP
     *
     * @param string       $ip    IPv4 или IPv6 адрес для проверки
     * @param string|array $range Диапазон / IP / CIDR
     * @return bool
     */
    private function ipMatchesRange(string $ip, $range) {

        // Диапазон
        if (is_array($range) || strpos($range, '-') !== false) {

            [$start, $end] = is_array($range) ? $range : array_map('trim', explode('-', $range, 2));

            return $this->ipInRangeBounds($ip, $start, $end);
        }

        // CIDR
        if (strpos($range, '/') !== false) {
            return $this->ipInCidr($ip, $range);
        }

        return $ip === $range;
    }

    /**
     * Проверка IP в диапазоне от $start до $end
     *
     * @param string $ip    IPv4 или IPv6 адрес для проверки
     * @param string $start IP, с которого начинается диапазон
     * @param string $end   IP, которым заканчивается диапазон
     * @return bool
     */
    private function ipInRangeBounds(string $ip, string $start, string $end) {

        $ip_bin    = inet_pton($ip);
        $start_bin = inet_pton($start);
        $end_bin   = inet_pton($end);

        if ($ip_bin === false || $start_bin === false || $end_bin === false) {
            return false;
        }

        return $ip_bin >= $start_bin && $ip_bin <= $end_bin;
    }

    /**
     * Проверка IP в CIDR-диапазоне
     *
     * @param string $ip   IPv4 или IPv6 адрес для проверки
     * @param string $cidr Сеть в формате CIDR
     * @return bool
     */
    private function ipInCidr(string $ip, string $cidr) {

        [$subnet, $prefix_length] = explode('/', $cidr, 2);

        $ip_bin     = inet_pton($ip);
        $subnet_bin = inet_pton($subnet);

        $prefix_length = (int) $prefix_length;

        if ($ip_bin === false || $subnet_bin === false) {
            return false;
        }

        $maxLen = strlen($ip_bin); // 4 для IPv4, 16 для IPv6

        $bytes = intdiv($prefix_length, 8);
        $bits  = $prefix_length % 8;

        $mask = str_repeat("\xff", $bytes);

        if ($bits !== 0) {
            $mask .= chr(0xff << (8 - $bits));
        }

        $mask = str_pad($mask, $maxLen, "\0");

        return ($ip_bin & $mask) === ($subnet_bin & $mask);
    }

}
