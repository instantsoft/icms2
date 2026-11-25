<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Data;

use GlobusStudio\QRCode\Encoder\BitBuffer;

abstract class AbstractQRData implements QRDataInterface
{
    public const MODE_NUMBER = 1;
    public const MODE_ALPHA_NUM = 2;
    public const MODE_8BIT_BYTE = 4;
    public const MODE_KANJI = 8;

    protected int $mode;
    protected string $data;

    public function __construct(int $mode, string $data)
    {
        $this->mode = $mode;
        $this->data = $data;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getLength(): int
    {
        return strlen($this->data);
    }

    public function getLengthInBits(int $version): int
    {
        if ($version < 1 || $version > 40) {
            throw new \InvalidArgumentException("Invalid version: $version");
        }

        if ($version <= 9) {
            switch ($this->mode) {
                case self::MODE_NUMBER:
                    return 10;
                case self::MODE_ALPHA_NUM:
                    return 9;
                case self::MODE_8BIT_BYTE:
                    return 8;
                case self::MODE_KANJI:
                    return 8;
            }
        } elseif ($version <= 26) {
            switch ($this->mode) {
                case self::MODE_NUMBER:
                    return 12;
                case self::MODE_ALPHA_NUM:
                    return 11;
                case self::MODE_8BIT_BYTE:
                    return 16;
                case self::MODE_KANJI:
                    return 10;
            }
        } else {
            switch ($this->mode) {
                case self::MODE_NUMBER:
                    return 14;
                case self::MODE_ALPHA_NUM:
                    return 13;
                case self::MODE_8BIT_BYTE:
                    return 16;
                case self::MODE_KANJI:
                    return 12;
            }
        }

        throw new \InvalidArgumentException("Invalid version: $version"); // @codeCoverageIgnore
    }

    abstract public function write(BitBuffer $buffer): void;
}
