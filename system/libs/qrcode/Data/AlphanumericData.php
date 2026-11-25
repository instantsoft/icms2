<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Data;

use GlobusStudio\QRCode\Encoder\BitBuffer;

final class AlphanumericData extends AbstractQRData
{
    private const CHAR_MAP = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4,
        '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14,
        'F' => 15, 'G' => 16, 'H' => 17, 'I' => 18, 'J' => 19,
        'K' => 20, 'L' => 21, 'M' => 22, 'N' => 23, 'O' => 24,
        'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28, 'T' => 29,
        'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
        'Z' => 35, ' ' => 36, '$' => 37, '%' => 38, '*' => 39,
        '+' => 40, '-' => 41, '.' => 42, '/' => 43, ':' => 44,
    ];

    public function __construct(string $data)
    {
        if (!self::isValid($data)) {
            throw new \InvalidArgumentException('Data contains invalid alphanumeric characters');
        }

        parent::__construct(self::MODE_ALPHA_NUM, $data);
    }

    public static function isValid(string $data): bool
    {
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            if (!isset(self::CHAR_MAP[$data[$i]])) {
                return false;
            }
        }

        return $len > 0;
    }

    public function write(BitBuffer $buffer): void
    {
        $data = $this->getData();
        $len = strlen($data);
        $i = 0;

        while ($i + 1 < $len) {
            $buffer->put(
                self::getCode($data[$i]) * 45 + self::getCode($data[$i + 1]),
                11
            );
            $i += 2;
        }

        if ($i < $len) {
            $buffer->put(self::getCode($data[$i]), 6);
        }
    }

    private static function getCode(string $char): int
    {
        if (!isset(self::CHAR_MAP[$char])) {
            throw new \InvalidArgumentException("Invalid alphanumeric character: $char"); // @codeCoverageIgnore
        }

        return self::CHAR_MAP[$char];
    }
}
