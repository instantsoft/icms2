<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\ErrorCorrection;

use GlobusStudio\QRCode\Math\GaloisField;

final class Polynomial
{
    /** @var int[] */
    private array $coefficients;

    /**
     * @param int[] $num
     */
    public function __construct(array $num, int $shift = 0)
    {
        $offset = 0;
        $count = count($num);

        while ($offset < $count && $num[$offset] === 0) {
            $offset++;
        }

        if ($offset >= $count) {
            $size = $shift > 0 ? $shift : 1;
            /** @var int[] $filled */
            $filled = array_fill(0, $size, 0);
            $this->coefficients = $filled;

            return;
        }

        $size = $count - $offset + $shift;
        /** @var int[] $filled */
        $filled = array_fill(0, $size, 0);
        $this->coefficients = $filled;

        for ($i = 0; $i < $count - $offset; $i++) {
            $this->coefficients[$i] = $num[$i + $offset];
        }
    }

    public function get(int $index): int
    {
        return $this->coefficients[$index];
    }

    public function getLength(): int
    {
        return count($this->coefficients);
    }

    public function multiply(self $other): self
    {
        /** @var int[] $num */
        $num = array_fill(0, $this->getLength() + $other->getLength() - 1, 0);

        for ($i = 0; $i < $this->getLength(); $i++) {
            $vi = GaloisField::log($this->get($i));

            for ($j = 0; $j < $other->getLength(); $j++) {
                $num[$i + $j] ^= GaloisField::exp($vi + GaloisField::log($other->get($j)));
            }
        }

        return new self($num);
    }

    public function mod(self $other): self
    {
        $current = $this;

        while ($current->getLength() - $other->getLength() >= 0) {
            $ratio = GaloisField::log($current->get(0)) - GaloisField::log($other->get(0));

            /** @var int[] $num */
            $num = array_fill(0, $current->getLength(), 0);
            for ($i = 0; $i < $current->getLength(); $i++) {
                $num[$i] = $current->get($i);
            }

            for ($i = 0; $i < $other->getLength(); $i++) {
                $num[$i] ^= GaloisField::exp(GaloisField::log($other->get($i)) + $ratio);
            }

            $current = new self($num);
        }

        return $current;
    }
}
