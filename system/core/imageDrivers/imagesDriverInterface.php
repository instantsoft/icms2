<?php

namespace icms\core\imageDrivers;

interface imagesDriverInterface {

    const DEFAULT_QLTY_JPG  = 90;
    const DEFAULT_QLTY_WEBP = 90;
    const DEFAULT_QLTY_PNG  = 9;

    public static function isAvailable();

    public function title();

    public function load(string $filename, string $image_type);

    public function isSourceAnimate();

    public function getWidth();

    public function getHeight();

    public function save(
        string $filename,
        int $image_type,
        ?int $quality,
        array $geometry,
        array $options
    );

    public function destroy();
}
