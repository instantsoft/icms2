<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Math;

final class GaloisField
{
    /** @var int[]|null */
    private static ?array $expTable = null;

    /** @var int[]|null */
    private static ?array $logTable = null;

    public static function init(): void
    {
        if (self::$expTable !== null) {
            return;
        }

        self::$expTable = array_fill(0, 256, 0);
        self::$logTable = array_fill(0, 256, 0);

        for ($i = 0; $i < 8; $i++) {
            self::$expTable[$i] = 1 << $i;
        }

        for ($i = 8; $i < 256; $i++) {
            self::$expTable[$i] = self::$expTable[$i - 4]
                ^ self::$expTable[$i - 5]
                ^ self::$expTable[$i - 6]
                ^ self::$expTable[$i - 8];
        }

        for ($i = 0; $i < 255; $i++) {
            self::$logTable[self::$expTable[$i]] = $i;
        }
    }

    public static function exp(int $n): int
    {
        self::init();
        assert(self::$expTable !== null);

        while ($n < 0) {
            $n += 255;
        }

        while ($n >= 256) {
            $n -= 255;
        }

        return self::$expTable[$n];
    }

    public static function log(int $n): int
    {
        self::init();
        assert(self::$logTable !== null);

        if ($n < 1) {
            throw new \InvalidArgumentException("Cannot compute log of $n in GF(256)");
        }

        return self::$logTable[$n];
    }
}
