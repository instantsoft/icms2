<?php

namespace icms\controllers\languages;

interface translatable {

    /**
     * Переводит текст с языка $source_lang на $target_lang
     *
     * @param string $source_lang Язык текста, который переводим
     * @param string $target_lang Язык текста, в который переводим
     * @param string $text Текст для перевода
     * @return string Переведённый текст
     */
    public function translate($source_lang, $target_lang, $text);
}
