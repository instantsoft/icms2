<?php
/**
 * Контроллер для шаблонов на основе Bootstrap
 * https://getbootstrap.com/
 */
class bootstrap extends cmsFrontend {

    const VENDORS = ['bootstrap4', 'bootstrap5'];

    public function isSupportedVendor($vendor) {
        return in_array($vendor, self::VENDORS, true);
    }

}
