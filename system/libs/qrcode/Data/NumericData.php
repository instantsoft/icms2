<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Data;

use GlobusStudio\QRCode\Encoder\BitBuffer;

final class NumericData extends AbstractQRData
{
    public function __construct(string $data)
    {
        if (!self::isValid($data)) {
            throw new \InvalidArgumentException('Data contains non-numeric characters');
        }

        parent::__construct(self::MODE_NUMBER, $data);
    }

    public static function isValid(string $data): bool
    {
        return preg_match('/^[0-9]+$/', $data) === 1;
    }

    public function write(BitBuffer $buffer): void
    {
        $data = $this->getData();
        $len = strlen($data);
        $i = 0;

        while ($i + 2 < $len) {
            $num = (int) substr($data, $i, 3);
            $buffer->put($num, 10);
            $i += 3;
        }

        if ($i < $len) {
            $remaining = $len - $i;

            if ($remaining === 1) {
                $num = (int) substr($data, $i, 1);
                $buffer->put($num, 4);
            } elseif ($remaining === 2) {
                $num = (int) substr($data, $i, 2);
                $buffer->put($num, 7);
            }
        }
    }
}
