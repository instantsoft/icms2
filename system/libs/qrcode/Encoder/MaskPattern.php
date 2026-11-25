<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Encoder;

final class MaskPattern
{
    public const PATTERN_000 = 0;
    public const PATTERN_001 = 1;
    public const PATTERN_010 = 2;
    public const PATTERN_011 = 3;
    public const PATTERN_100 = 4;
    public const PATTERN_101 = 5;
    public const PATTERN_110 = 6;
    public const PATTERN_111 = 7;

    public static function getMask(int $pattern, int $i, int $j): bool
    {
        switch ($pattern) {
            case self::PATTERN_000:
                return ($i + $j) % 2 === 0;
            case self::PATTERN_001:
                return $i % 2 === 0;
            case self::PATTERN_010:
                return $j % 3 === 0;
            case self::PATTERN_011:
                return ($i + $j) % 3 === 0;
            case self::PATTERN_100:
                return ((int) ($i / 2) + (int) ($j / 3)) % 2 === 0;
            case self::PATTERN_101:
                return ($i * $j) % 2 + ($i * $j) % 3 === 0;
            case self::PATTERN_110:
                return (($i * $j) % 2 + ($i * $j) % 3) % 2 === 0;
            case self::PATTERN_111:
                return (($i * $j) % 3 + ($i + $j) % 2) % 2 === 0;
            default:
                throw new \InvalidArgumentException("Invalid mask pattern: $pattern");
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    public static function getBestPattern(array $modules, int $moduleCount): int
    {
        $minLostPoint = PHP_INT_MAX;
        $bestPattern = 0;

        for ($i = 0; $i < 8; $i++) {
            $lostPoint = self::calculateLostPoint($modules, $moduleCount, $i);

            if ($lostPoint < $minLostPoint) {
                $minLostPoint = $lostPoint;
                $bestPattern = $i;
            }
        }

        return $bestPattern;
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function calculateLostPoint(array $modules, int $moduleCount, int $maskPattern): int
    {
        $maskedModules = self::applyMask($modules, $moduleCount, $maskPattern);

        return self::getLostPoint($maskedModules, $moduleCount);
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     * @return array<int, array<int, bool>>
     */
    private static function applyMask(array $modules, int $moduleCount, int $maskPattern): array
    {
        $result = [];

        for ($row = 0; $row < $moduleCount; $row++) {
            $result[$row] = [];
            for ($col = 0; $col < $moduleCount; $col++) {
                if ($modules[$row][$col] === null) {
                    $result[$row][$col] = self::getMask($maskPattern, $row, $col);
                } else {
                    $result[$row][$col] = $modules[$row][$col];
                }
            }
        }

        return $result;
    }

    /**
     * @param array<int, array<int, bool>> $modules
     */
    public static function getLostPoint(array $modules, int $moduleCount): int
    {
        $lostPoint = 0;

        for ($row = 0; $row < $moduleCount; $row++) {
            for ($col = 0; $col < $moduleCount; $col++) {
                $sameCount = 0;
                $dark = $modules[$row][$col];

                for ($r = -1; $r <= 1; $r++) {
                    if ($row + $r < 0 || $moduleCount <= $row + $r) {
                        continue;
                    }

                    for ($c = -1; $c <= 1; $c++) {
                        if ($col + $c < 0 || $moduleCount <= $col + $c) {
                            continue;
                        }

                        if ($r === 0 && $c === 0) {
                            continue;
                        }

                        if ($dark === $modules[$row + $r][$col + $c]) {
                            $sameCount++;
                        }
                    }
                }

                if ($sameCount > 5) {
                    $lostPoint += (3 + $sameCount - 5);
                }
            }
        }

        for ($row = 0; $row < $moduleCount - 1; $row++) {
            for ($col = 0; $col < $moduleCount - 1; $col++) {
                $count = 0;
                if ($modules[$row][$col]) {
                    $count++;
                }
                if ($modules[$row + 1][$col]) {
                    $count++;
                }
                if ($modules[$row][$col + 1]) {
                    $count++;
                }
                if ($modules[$row + 1][$col + 1]) {
                    $count++;
                }
                if ($count === 0 || $count === 4) {
                    $lostPoint += 3;
                }
            }
        }

        for ($row = 0; $row < $moduleCount; $row++) {
            for ($col = 0; $col < $moduleCount - 6; $col++) {
                if ($modules[$row][$col]
                    && !$modules[$row][$col + 1]
                    && $modules[$row][$col + 2]
                    && $modules[$row][$col + 3]
                    && $modules[$row][$col + 4]
                    && !$modules[$row][$col + 5]
                    && $modules[$row][$col + 6]) {
                    $lostPoint += 40;
                }
            }
        }

        for ($col = 0; $col < $moduleCount; $col++) {
            for ($row = 0; $row < $moduleCount - 6; $row++) {
                if ($modules[$row][$col]
                    && !$modules[$row + 1][$col]
                    && $modules[$row + 2][$col]
                    && $modules[$row + 3][$col]
                    && $modules[$row + 4][$col]
                    && !$modules[$row + 5][$col]
                    && $modules[$row + 6][$col]) {
                    $lostPoint += 40;
                }
            }
        }

        $darkCount = 0;
        for ($col = 0; $col < $moduleCount; $col++) {
            for ($row = 0; $row < $moduleCount; $row++) {
                if ($modules[$row][$col]) {
                    $darkCount++;
                }
            }
        }

        $ratio = (int) (abs(100 * $darkCount / $moduleCount / $moduleCount - 50) / 5);
        $lostPoint += $ratio * 10;

        return $lostPoint;
    }
}
