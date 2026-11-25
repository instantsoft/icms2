<?php

declare(strict_types=1);

namespace GlobusStudio\QRCode\Renderer;

final class RawRenderer implements RendererInterface
{
    /**
     * @param bool[][] $matrix
     */
    public function render(array $matrix): string
    {
        $result = [];
        foreach ($matrix as $row) {
            $line = [];
            foreach ($row as $cell) {
                $line[] = $cell ? 1 : 0;
            }
            $result[] = $line;
        }

        $json = json_encode($result);

        if ($json === false) {
            throw new \RuntimeException('Failed to encode matrix to JSON');
        }

        return $json;
    }

    /**
     * @param bool[][] $matrix
     * @return int[][]
     */
    public function toArray(array $matrix): array
    {
        $result = [];
        foreach ($matrix as $row) {
            $line = [];
            foreach ($row as $cell) {
                $line[] = $cell ? 1 : 0;
            }
            $result[] = $line;
        }

        return $result;
    }
}
