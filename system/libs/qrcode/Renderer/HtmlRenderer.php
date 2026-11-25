<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

final class HtmlRenderer implements RendererInterface
{
    private string $size;
    private string $foreground;
    private string $background;

    /**
     * @param array{size?: string, foreground?: string, background?: string} $options
     */
    public function __construct(array $options = [])
    {
        $this->size = $options['size'] ?? '2px';
        $this->foreground = $options['foreground'] ?? '#000000';
        $this->background = $options['background'] ?? '#ffffff';
    }

    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string
    {
        $moduleCount = count($matrix);
        $size = self::escape($this->size);
        $fg = self::escape($this->foreground);
        $bg = self::escape($this->background);

        $style = 'border-style:none;border-collapse:collapse;margin:0;padding:0;';

        $html = '<table style="' . $style . '">';

        for ($r = 0; $r < $moduleCount; $r++) {
            $html .= '<tr style="' . $style . '">';

            for ($c = 0; $c < $moduleCount; $c++) {
                $color = $matrix[$r][$c] ? $fg : $bg;
                $html .= '<td style="' . $style . 'width:' . $size . ';height:' . $size
                    . ';background-color:' . $color . '"></td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
