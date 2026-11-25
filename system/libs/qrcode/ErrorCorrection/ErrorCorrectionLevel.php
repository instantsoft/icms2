<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\ErrorCorrection;

final class ErrorCorrectionLevel
{
    public const L = 1;
    public const M = 0;
    public const Q = 3;
    public const H = 2;

    private const VALID = [self::L, self::M, self::Q, self::H];

    public static function isValid(int $level): bool
    {
        return in_array($level, self::VALID, true);
    }

    public static function validate(int $level): int
    {
        if (!self::isValid($level)) {
            throw new \InvalidArgumentException("Invalid error correction level: $level");
        }

        return $level;
    }
}
