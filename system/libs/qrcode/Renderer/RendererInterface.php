<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

interface RendererInterface
{
    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string;
}
