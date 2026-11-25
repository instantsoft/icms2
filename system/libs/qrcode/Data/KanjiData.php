<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Data;

use GlobusStudio\QRCode\Encoder\BitBuffer;

final class KanjiData extends AbstractQRData
{
    public function __construct(string $data)
    {
        if (!self::isValid($data)) {
            throw new \InvalidArgumentException('Data is not valid Shift-JIS Kanji');
        }

        parent::__construct(self::MODE_KANJI, $data);
    }

    public static function isValid(string $data): bool
    {
        $len = strlen($data);

        if ($len === 0 || $len % 2 !== 0) {
            return false;
        }

        $i = 0;
        while ($i + 1 < $len) {
            $c = ((0xFF & ord($data[$i])) << 8) | (0xFF & ord($data[$i + 1]));

            if (!(0x8140 <= $c && $c <= 0x9FFC) && !(0xE040 <= $c && $c <= 0xEBBF)) {
                return false;
            }

            $i += 2;
        }

        return true;
    }

    public function getLength(): int
    {
        return (int) (strlen($this->data) / 2);
    }

    public function write(BitBuffer $buffer): void
    {
        $data = $this->getData();
        $len = strlen($data);
        $i = 0;

        while ($i + 1 < $len) {
            $c = ((0xFF & ord($data[$i])) << 8) | (0xFF & ord($data[$i + 1]));

            if (0x8140 <= $c && $c <= 0x9FFC) {
                $c -= 0x8140;
            } elseif (0xE040 <= $c && $c <= 0xEBBF) {
                $c -= 0xC140;
            }

            $c = (($c >> 8) & 0xFF) * 0xC0 + ($c & 0xFF);
            $buffer->put($c, 13);

            $i += 2;
        }
    }
}
