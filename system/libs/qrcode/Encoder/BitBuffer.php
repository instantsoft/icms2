<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Encoder;

final class BitBuffer
{
    /** @var int[] */
    private array $buffer = [];

    private int $length = 0;

    /**
     * @return int[]
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    public function getLengthInBits(): int
    {
        return $this->length;
    }

    public function get(int $index): bool
    {
        $bufIndex = (int) ($index / 8);

        return (($this->buffer[$bufIndex] >> (7 - $index % 8)) & 1) === 1;
    }

    public function put(int $num, int $length): void
    {
        for ($i = 0; $i < $length; $i++) {
            $this->putBit((($num >> ($length - $i - 1)) & 1) === 1);
        }
    }

    public function putBit(bool $bit): void
    {
        $bufIndex = (int) ($this->length / 8);

        if (count($this->buffer) <= $bufIndex) {
            $this->buffer[] = 0;
        }

        if ($bit) {
            $this->buffer[$bufIndex] |= (0x80 >> ($this->length % 8));
        }

        $this->length++;
    }
}
