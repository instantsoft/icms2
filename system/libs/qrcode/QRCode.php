<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode;

use GlobusStudio\QRCode\Encoder\Encoder;
use GlobusStudio\QRCode\ErrorCorrection\ErrorCorrectionLevel;
use GlobusStudio\QRCode\Renderer\HtmlRenderer;
use GlobusStudio\QRCode\Renderer\PngRenderer;
use GlobusStudio\QRCode\Renderer\RendererInterface;
use GlobusStudio\QRCode\Renderer\StringRenderer;
use GlobusStudio\QRCode\Renderer\SvgRenderer;

final class QRCode
{
    private string $data;
    private int $errorCorrectionLevel;
    private ?int $version;
    private ?int $maskPattern;

    /** @var bool[][]|null */
    private ?array $matrix = null;

    public function __construct(string $data, int $errorCorrectionLevel = ErrorCorrectionLevel::M, ?int $version = null, ?int $maskPattern = null)
    {
        if ($data === '') {
            throw new \InvalidArgumentException('Data cannot be empty');
        }

        $this->data = $data;
        $this->errorCorrectionLevel = ErrorCorrectionLevel::validate($errorCorrectionLevel);
        $this->version = $version;
        $this->maskPattern = $maskPattern;
    }

    /**
     * @return bool[][]
     */
    public function getMatrix(): array
    {
        if ($this->matrix === null) {
            $this->matrix = Encoder::encode($this->data, $this->errorCorrectionLevel, $this->version, $this->maskPattern);
        }

        return $this->matrix;
    }

    public function render(RendererInterface $renderer): string
    {
        return $renderer->render($this->getMatrix());
    }

    /**
     * @param array{level?: int, mask?: int, size?: int, margin?: int, foreground?: string, background?: string} $options
     */
    public static function svg(string $data, array $options = []): string
    {
        $qr = new self($data, $options['level'] ?? ErrorCorrectionLevel::M, null, $options['mask'] ?? null);

        return $qr->render(new SvgRenderer([
            'size' => $options['size'] ?? 2,
            'margin' => $options['margin'] ?? 2,
            'foreground' => $options['foreground'] ?? '#000000',
            'background' => $options['background'] ?? '#ffffff',
        ]));
    }

    /**
     * @param array{level?: int, mask?: int, size?: int, margin?: int, foreground?: int, background?: int} $options
     */
    public static function png(string $data, array $options = []): string
    {
        $qr = new self($data, $options['level'] ?? ErrorCorrectionLevel::M, null, $options['mask'] ?? null);

        $renderer = new PngRenderer([
            'size' => $options['size'] ?? 4,
            'margin' => $options['margin'] ?? 4,
            'foreground' => $options['foreground'] ?? 0x000000,
            'background' => $options['background'] ?? 0xFFFFFF,
        ]);

        return $renderer->renderDataUri($qr->getMatrix());
    }

    /**
     * @param array{level?: int, mask?: int, size?: string, foreground?: string, background?: string} $options
     */
    public static function html(string $data, array $options = []): string
    {
        $qr = new self($data, $options['level'] ?? ErrorCorrectionLevel::M, null, $options['mask'] ?? null);

        return $qr->render(new HtmlRenderer([
            'size' => $options['size'] ?? '2px',
            'foreground' => $options['foreground'] ?? '#000000',
            'background' => $options['background'] ?? '#ffffff',
        ]));
    }

    /**
     * @param array{level?: int, mask?: int, dark?: string, light?: string, margin?: int} $options
     */
    public static function string(string $data, array $options = []): string
    {
        $qr = new self($data, $options['level'] ?? ErrorCorrectionLevel::M, null, $options['mask'] ?? null);

        return $qr->render(new StringRenderer([
            'dark' => $options['dark'] ?? "\xe2\x96\x88\xe2\x96\x88",
            'light' => $options['light'] ?? '  ',
            'margin' => $options['margin'] ?? 1,
        ]));
    }

    /**
     * @param array{level?: int, mask?: int} $options
     * @return bool[][]
     */
    public static function matrix(string $data, array $options = []): array
    {
        $qr = new self($data, $options['level'] ?? ErrorCorrectionLevel::M, null, $options['mask'] ?? null);

        return $qr->getMatrix();
    }
}
