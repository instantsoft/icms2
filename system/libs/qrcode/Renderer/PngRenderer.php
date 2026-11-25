<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

final class PngRenderer implements RendererInterface
{
    private int $size;
    private int $margin;
    private int $foreground;
    private int $background;

    /**
     * @param array{size?: int, margin?: int, foreground?: int, background?: int} $options
     */
    public function __construct(array $options = [])
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('The GD extension is required for PNG rendering'); // @codeCoverageIgnore
        }

        $this->size = max(1, (int) ($options['size'] ?? 4));
        $this->margin = max(0, (int) ($options['margin'] ?? 4));
        $this->foreground = (int) ($options['foreground'] ?? 0x000000);
        $this->background = (int) ($options['background'] ?? 0xFFFFFF);
    }

    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string
    {
        $moduleCount = count($matrix);
        $imageSize = max(1, $moduleCount * $this->size + $this->margin * 2);

        $image = imagecreatetruecolor($imageSize, $imageSize);

        if ($image === false) {
            throw new \RuntimeException('Failed to create image'); // @codeCoverageIgnore
        }

        $fgColor = imagecolorallocate(
            $image,
            ($this->foreground >> 16) & 0xFF,
            ($this->foreground >> 8) & 0xFF,
            $this->foreground & 0xFF
        );

        $bgColor = imagecolorallocate(
            $image,
            ($this->background >> 16) & 0xFF,
            ($this->background >> 8) & 0xFF,
            $this->background & 0xFF
        );

        if ($fgColor === false || $bgColor === false) {
            throw new \RuntimeException('Failed to allocate colors'); // @codeCoverageIgnore
        }

        imagefilledrectangle($image, 0, 0, $imageSize - 1, $imageSize - 1, $bgColor);

        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($matrix[$r][$c]) {
                    imagefilledrectangle(
                        $image,
                        $this->margin + $c * $this->size,
                        $this->margin + $r * $this->size,
                        $this->margin + ($c + 1) * $this->size - 1,
                        $this->margin + ($r + 1) * $this->size - 1,
                        $fgColor
                    );
                }
            }
        }

        ob_start();
        imagepng($image);
        $data = ob_get_clean();

        if (PHP_VERSION_ID < 80000) {
            imagedestroy($image); // @codeCoverageIgnore
        }

        if ($data === false) {
            throw new \RuntimeException('Failed to capture PNG output'); // @codeCoverageIgnore
        }

        return $data;
    }

    /**
     * @param bool[][] $matrix
     */
    public function renderDataUri(array $matrix): string
    {
        return 'data:image/png;base64,' . base64_encode($this->render($matrix));
    }
}
