<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

final class SvgRenderer implements RendererInterface
{
    private int $size;
    private int $margin;
    private string $foreground;
    private string $background;

    /**
     * @param array{size?: int, margin?: int, foreground?: string, background?: string} $options
     */
    public function __construct(array $options = [])
    {
        $this->size = max(1, (int) ($options['size'] ?? 2));
        $this->margin = max(0, (int) ($options['margin'] ?? 2));
        $this->foreground = $options['foreground'] ?? '#000000';
        $this->background = $options['background'] ?? '#ffffff';
    }

    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string
    {
        $moduleCount = count($matrix);
        $width = $moduleCount * $this->size + $this->margin * 2;
        $height = $width;

        $fg = self::escapeAttr($this->foreground);
        $bg = self::escapeAttr($this->background);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height
            . '" viewBox="0 0 ' . $width . ' ' . $height . '">';

        $svg .= '<rect width="' . $width . '" height="' . $height . '" fill="' . $bg . '"/>';

        $path = '';
        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($matrix[$r][$c]) {
                    $x = $this->margin + $c * $this->size;
                    $y = $this->margin + $r * $this->size;
                    $path .= 'M' . $x . ',' . $y . 'h' . $this->size . 'v' . $this->size . 'h-' . $this->size . 'z';
                }
            }
        }

        if ($path !== '') {
            $svg .= '<path d="' . $path . '" fill="' . $fg . '" shape-rendering="crispEdges"/>';
        }

        $svg .= '</svg>';

        return $svg;
    }

    private static function escapeAttr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
