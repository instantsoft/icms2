<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

final class StringRenderer implements RendererInterface
{
    private string $darkChar;
    private string $lightChar;
    private int $margin;

    /**
     * @param array{dark?: string, light?: string, margin?: int} $options
     */
    public function __construct(array $options = [])
    {
        $this->darkChar = $options['dark'] ?? "\xe2\x96\x88\xe2\x96\x88";
        $this->lightChar = $options['light'] ?? '  ';
        $this->margin = max(0, (int) ($options['margin'] ?? 1));
    }

    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string
    {
        $moduleCount = count($matrix);
        $output = '';

        $marginLine = str_repeat($this->lightChar, $moduleCount + $this->margin * 2);

        for ($i = 0; $i < $this->margin; $i++) {
            $output .= $marginLine . "\n";
        }

        for ($r = 0; $r < $moduleCount; $r++) {
            $line = str_repeat($this->lightChar, $this->margin);

            for ($c = 0; $c < $moduleCount; $c++) {
                $line .= $matrix[$r][$c] ? $this->darkChar : $this->lightChar;
            }

            $line .= str_repeat($this->lightChar, $this->margin);
            $output .= $line . "\n";
        }

        for ($i = 0; $i < $this->margin; $i++) {
            $output .= $marginLine . "\n";
        }

        return $output;
    }
}
