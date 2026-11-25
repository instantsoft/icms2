<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Data;

use GlobusStudio\QRCode\Encoder\BitBuffer;

interface QRDataInterface
{
    public function getMode(): int;

    public function getLength(): int;

    public function getLengthInBits(int $version): int;

    public function write(BitBuffer $buffer): void;
}
