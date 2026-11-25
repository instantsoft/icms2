<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Encoder;

use GlobusStudio\QRCode\Data\AbstractQRData;
use GlobusStudio\QRCode\Data\AlphanumericData;
use GlobusStudio\QRCode\Data\ByteData;
use GlobusStudio\QRCode\Data\KanjiData;
use GlobusStudio\QRCode\Data\NumericData;
use GlobusStudio\QRCode\Data\QRDataInterface;
use GlobusStudio\QRCode\ErrorCorrection\ErrorCorrectionLevel;
use GlobusStudio\QRCode\ErrorCorrection\Polynomial;
use GlobusStudio\QRCode\ErrorCorrection\RSBlock;
use GlobusStudio\QRCode\Math\GaloisField;
use GlobusStudio\QRCode\Util\ModeDetector;
use GlobusStudio\QRCode\Util\Version;

final class Encoder
{
    private const PAD0 = 0xEC;
    private const PAD1 = 0x11;

    private const G15 = (1 << 10) | (1 << 8) | (1 << 5) | (1 << 4) | (1 << 2) | (1 << 1) | (1 << 0);
    private const G18 = (1 << 12) | (1 << 11) | (1 << 10) | (1 << 9) | (1 << 8) | (1 << 5) | (1 << 2) | (1 << 0);
    private const G15_MASK = (1 << 14) | (1 << 12) | (1 << 10) | (1 << 4) | (1 << 1);

    /**
     * @return bool[][]
     */
    public static function encode(string $data, int $errorCorrectionLevel = ErrorCorrectionLevel::M, ?int $version = null, ?int $maskPattern = null): array
    {
        ErrorCorrectionLevel::validate($errorCorrectionLevel);

        if ($maskPattern !== null && ($maskPattern < 0 || $maskPattern > 7)) {
            throw new \InvalidArgumentException("Invalid mask pattern: $maskPattern (must be 0-7)");
        }

        $mode = ModeDetector::detect($data);
        $qrData = self::createDataObject($data, $mode);

        if ($version === null) {
            $version = Version::getMinimumVersion(
                $qrData->getLength(),
                $mode,
                $errorCorrectionLevel
            );
        }

        $moduleCount = $version * 4 + 17;
        $modules = self::createNullMatrix($moduleCount);

        self::setupPositionProbePattern($modules, $moduleCount, 0, 0);
        self::setupPositionProbePattern($modules, $moduleCount, $moduleCount - 7, 0);
        self::setupPositionProbePattern($modules, $moduleCount, 0, $moduleCount - 7);
        self::setupPositionAdjustPattern($modules, $moduleCount, $version);
        self::setupTimingPattern($modules, $moduleCount);

        $bestPattern = $maskPattern ?? self::findBestMaskPattern($modules, $moduleCount, $version, $errorCorrectionLevel, [$qrData]);

        self::setupTypeInfo($modules, $moduleCount, false, $bestPattern, $errorCorrectionLevel);

        if ($version >= 7) {
            self::setupTypeNumber($modules, $moduleCount, false, $version);
        }

        $encodedData = self::createEncodedData($version, $errorCorrectionLevel, [$qrData]);
        self::mapData($modules, $moduleCount, $encodedData, $bestPattern);

        return self::toBoolMatrix($modules, $moduleCount);
    }

    private static function createDataObject(string $data, int $mode): QRDataInterface
    {
        switch ($mode) {
            case AbstractQRData::MODE_NUMBER:
                return new NumericData($data);
            case AbstractQRData::MODE_ALPHA_NUM:
                return new AlphanumericData($data);
            case AbstractQRData::MODE_KANJI:
                return new KanjiData($data);
            default:
                return new ByteData($data);
        }
    }

    /**
     * @return array<int, array<int, bool|null>>
     */
    private static function createNullMatrix(int $size): array
    {
        $matrix = [];
        for ($i = 0; $i < $size; $i++) {
            $matrix[$i] = array_fill(0, $size, null);
        }

        return $matrix;
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function setupPositionProbePattern(array &$modules, int $moduleCount, int $row, int $col): void
    {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                if ($row + $r <= -1 || $moduleCount <= $row + $r
                    || $col + $c <= -1 || $moduleCount <= $col + $c) {
                    continue;
                }

                $modules[$row + $r][$col + $c] =
                    (0 <= $r && $r <= 6 && ($c === 0 || $c === 6))
                    || (0 <= $c && $c <= 6 && ($r === 0 || $r === 6))
                    || (2 <= $r && $r <= 4 && 2 <= $c && $c <= 4);
            }
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function setupPositionAdjustPattern(array &$modules, int $moduleCount, int $version): void
    {
        $pos = Version::getPatternPosition($version);

        $posCount = count($pos);
        for ($i = 0; $i < $posCount; $i++) {
            for ($j = 0; $j < $posCount; $j++) {
                $row = $pos[$i];
                $col = $pos[$j];

                if ($modules[$row][$col] !== null) {
                    continue;
                }

                for ($r = -2; $r <= 2; $r++) {
                    for ($c = -2; $c <= 2; $c++) {
                        $modules[$row + $r][$col + $c] =
                            $r === -2 || $r === 2 || $c === -2 || $c === 2 || ($r === 0 && $c === 0);
                    }
                }
            }
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function setupTimingPattern(array &$modules, int $moduleCount): void
    {
        for ($i = 8; $i < $moduleCount - 8; $i++) {
            if ($modules[$i][6] !== null) {
                continue;
            }

            $modules[$i][6] = ($i % 2 === 0);
            $modules[6][$i] = ($i % 2 === 0);
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function setupTypeInfo(array &$modules, int $moduleCount, bool $test, int $maskPattern, int $errorCorrectionLevel): void
    {
        $data = ($errorCorrectionLevel << 3) | $maskPattern;
        $bits = self::getBCHTypeInfo($data);

        for ($i = 0; $i < 15; $i++) {
            $mod = (!$test && (($bits >> $i) & 1) === 1);

            if ($i < 6) {
                $modules[$i][8] = $mod;
            } elseif ($i < 8) {
                $modules[$i + 1][8] = $mod;
            } else {
                $modules[$moduleCount - 15 + $i][8] = $mod;
            }

            if ($i < 8) {
                $modules[8][$moduleCount - $i - 1] = $mod;
            } elseif ($i < 9) {
                $modules[8][15 - $i - 1 + 1] = $mod;
            } else {
                $modules[8][15 - $i - 1] = $mod;
            }
        }

        $modules[$moduleCount - 8][8] = !$test;
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     */
    private static function setupTypeNumber(array &$modules, int $moduleCount, bool $test, int $version): void
    {
        $bits = self::getBCHTypeNumber($version);

        for ($i = 0; $i < 18; $i++) {
            $mod = (!$test && (($bits >> $i) & 1) === 1);
            $modules[(int) ($i / 3)][$i % 3 + $moduleCount - 8 - 3] = $mod;
            $modules[$i % 3 + $moduleCount - 8 - 3][(int) ($i / 3)] = $mod;
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     * @param QRDataInterface[] $dataList
     */
    private static function findBestMaskPattern(array &$modules, int $moduleCount, int $version, int $errorCorrectionLevel, array $dataList): int
    {
        $minLostPoint = PHP_INT_MAX;
        $bestPattern = 0;

        for ($pattern = 0; $pattern < 8; $pattern++) {
            $testModules = $modules;

            self::setupTypeInfo($testModules, $moduleCount, true, $pattern, $errorCorrectionLevel);

            if ($version >= 7) {
                self::setupTypeNumber($testModules, $moduleCount, true, $version);
            }

            $data = self::createEncodedData($version, $errorCorrectionLevel, $dataList);
            self::mapData($testModules, $moduleCount, $data, $pattern);

            /** @var array<int, array<int, bool>> $boolMatrix */
            $boolMatrix = self::toBoolMatrix($testModules, $moduleCount);
            $lostPoint = MaskPattern::getLostPoint($boolMatrix, $moduleCount);

            if ($lostPoint < $minLostPoint) {
                $minLostPoint = $lostPoint;
                $bestPattern = $pattern;
            }
        }

        return $bestPattern;
    }

    /**
     * @param QRDataInterface[] $dataList
     * @return int[]
     */
    private static function createEncodedData(int $version, int $errorCorrectionLevel, array $dataList): array
    {
        $rsBlocks = RSBlock::getBlocks($version, $errorCorrectionLevel);

        $buffer = new BitBuffer();

        foreach ($dataList as $data) {
            $buffer->put($data->getMode(), 4);
            $buffer->put($data->getLength(), $data->getLengthInBits($version));
            $data->write($buffer);
        }

        $totalDataCount = 0;
        foreach ($rsBlocks as $block) {
            $totalDataCount += $block->getDataCount();
        }

        if ($buffer->getLengthInBits() > $totalDataCount * 8) {
            throw new \OverflowException(
                "Code length overflow: {$buffer->getLengthInBits()} > " . ($totalDataCount * 8)
            );
        }

        if ($buffer->getLengthInBits() + 4 <= $totalDataCount * 8) {
            $buffer->put(0, 4);
        }

        while ($buffer->getLengthInBits() % 8 !== 0) {
            $buffer->putBit(false);
        }

        while ($buffer->getLengthInBits() < $totalDataCount * 8) {
            $buffer->put(self::PAD0, 8);

            if ($buffer->getLengthInBits() >= $totalDataCount * 8) {
                break;
            }

            $buffer->put(self::PAD1, 8);
        }

        return self::createBytes($buffer, $rsBlocks);
    }

    /**
     * @param RSBlock[] $rsBlocks
     * @return int[]
     */
    private static function createBytes(BitBuffer $buffer, array $rsBlocks): array
    {
        $offset = 0;
        $maxDcCount = 0;
        $maxEcCount = 0;

        $rsBlockCount = count($rsBlocks);

        /** @var array<int, int[]> $dcdata */
        $dcdata = [];
        /** @var array<int, int[]> $ecdata */
        $ecdata = [];

        for ($r = 0; $r < $rsBlockCount; $r++) {
            $dcCount = $rsBlocks[$r]->getDataCount();
            $ecCount = $rsBlocks[$r]->getTotalCount() - $dcCount;

            $maxDcCount = max($maxDcCount, $dcCount);
            $maxEcCount = max($maxEcCount, $ecCount);

            /** @var int[] $dcRow */
            $dcRow = array_fill(0, $dcCount, 0);
            $bdata = $buffer->getBuffer();

            for ($i = 0; $i < $dcCount; $i++) {
                $dcRow[$i] = 0xFF & $bdata[$i + $offset];
            }
            $dcdata[$r] = $dcRow;
            $offset += $dcCount;

            $rsPoly = self::getErrorCorrectPolynomial($ecCount);
            $rawPoly = new Polynomial($dcdata[$r], $rsPoly->getLength() - 1);
            $modPoly = $rawPoly->mod($rsPoly);

            $ecDataCount = $rsPoly->getLength() - 1;
            /** @var int[] $ecRow */
            $ecRow = array_fill(0, $ecDataCount, 0);

            for ($i = 0; $i < $ecDataCount; $i++) {
                $modIndex = $i + $modPoly->getLength() - $ecDataCount;
                $ecRow[$i] = ($modIndex >= 0) ? $modPoly->get($modIndex) : 0;
            }
            $ecdata[$r] = $ecRow;
        }

        $totalCodeCount = 0;
        for ($i = 0; $i < $rsBlockCount; $i++) {
            $totalCodeCount += $rsBlocks[$i]->getTotalCount();
        }

        /** @var int[] $data */
        $data = array_fill(0, $totalCodeCount, 0);
        $index = 0;

        for ($i = 0; $i < $maxDcCount; $i++) {
            for ($r = 0; $r < $rsBlockCount; $r++) {
                if ($i < count($dcdata[$r])) {
                    $data[$index++] = $dcdata[$r][$i];
                }
            }
        }

        for ($i = 0; $i < $maxEcCount; $i++) {
            for ($r = 0; $r < $rsBlockCount; $r++) {
                if ($i < count($ecdata[$r])) {
                    $data[$index++] = $ecdata[$r][$i];
                }
            }
        }

        return $data;
    }

    private static function getErrorCorrectPolynomial(int $errorCorrectLength): Polynomial
    {
        $a = new Polynomial([1]);

        for ($i = 0; $i < $errorCorrectLength; $i++) {
            $a = $a->multiply(new Polynomial([1, GaloisField::exp($i)]));
        }

        return $a;
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     * @param int[] $data
     */
    private static function mapData(array &$modules, int $moduleCount, array $data, int $maskPattern): void
    {
        $inc = -1;
        $row = $moduleCount - 1;
        $bitIndex = 7;
        $byteIndex = 0;
        $dataCount = count($data);

        for ($col = $moduleCount - 1; $col > 0; $col -= 2) {
            if ($col === 6) {
                $col--;
            }

            while (true) {
                for ($c = 0; $c < 2; $c++) {
                    if ($modules[$row][$col - $c] === null) {
                        $dark = false;

                        if ($byteIndex < $dataCount) {
                            $dark = (($data[$byteIndex] >> $bitIndex) & 1) === 1;
                        }

                        if (MaskPattern::getMask($maskPattern, $row, $col - $c)) {
                            $dark = !$dark;
                        }

                        $modules[$row][$col - $c] = $dark;
                        $bitIndex--;

                        if ($bitIndex === -1) {
                            $byteIndex++;
                            $bitIndex = 7;
                        }
                    }
                }

                $row += $inc;

                if ($row < 0 || $moduleCount <= $row) {
                    $row -= $inc;
                    $inc = -$inc;
                    break;
                }
            }
        }
    }

    /**
     * @param array<int, array<int, bool|null>> $modules
     * @return bool[][]
     */
    private static function toBoolMatrix(array $modules, int $moduleCount): array
    {
        $result = [];

        for ($r = 0; $r < $moduleCount; $r++) {
            $result[$r] = [];
            for ($c = 0; $c < $moduleCount; $c++) {
                $result[$r][$c] = $modules[$r][$c] === true;
            }
        }

        return $result;
    }

    private static function getBCHTypeInfo(int $data): int
    {
        $d = $data << 10;

        while (self::getBCHDigit($d) - self::getBCHDigit(self::G15) >= 0) {
            $d ^= (self::G15 << (self::getBCHDigit($d) - self::getBCHDigit(self::G15)));
        }

        return (($data << 10) | $d) ^ self::G15_MASK;
    }

    private static function getBCHTypeNumber(int $data): int
    {
        $d = $data << 12;

        while (self::getBCHDigit($d) - self::getBCHDigit(self::G18) >= 0) {
            $d ^= (self::G18 << (self::getBCHDigit($d) - self::getBCHDigit(self::G18)));
        }

        return ($data << 12) | $d;
    }

    private static function getBCHDigit(int $data): int
    {
        $digit = 0;

        while ($data !== 0) {
            $digit++;
            $data >>= 1;
        }

        return $digit;
    }
}
