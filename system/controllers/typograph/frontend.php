<?php

class typograph extends cmsFrontend {

    private static $smiles;
    private $smiles_dir = 'static/smiles/';

    public function actionGetSmiles() {
        return $this->cms_template->renderJSON([
            'smiles' => $this->loadSmiles()->getSmiles()
        ]);
    }

    public function replaceEmotionToSmile($text) {

        $smiles_emotion = [
            ' :) ' => 'smile',
            ' =) ' => 'smile',
            ':-)'  => 'smile',
            ' :( ' => 'sad',
            ':-('  => 'sad',
            ';-)'  => 'wink',
            ' ;) ' => 'wink',
            ' :D ' => 'laugh',
            ':-D'  => 'laugh',
            '=-0'  => 'wonder',
            ':-0'  => 'wonder',
            ':-P'  => 'tongue'
        ];

        foreach ($smiles_emotion as $find => $tag) {
            $text = str_replace($find, ':' . $tag . ':', $text);
        }

        $smiles = $this->loadSmiles()->getSmiles();

        if ($smiles) {
            foreach ($smiles as $tag => $smile_path) {
                $text = str_replace(':' . $tag . ':', ' <img src="' . $smile_path . '" alt="' . $tag . '" /> ', $text);
            }
        }

        return $text;
    }

    private function loadSmiles() {

        if (self::$smiles !== null) {
            return $this;
        }

        $cache = cmsCache::getInstance();
        $cache_key = 'smiles';

        if (false !== (self::$smiles = $cache->get($cache_key))) {
            return $this;
        }

        self::$smiles = [];

        $pattern = $this->cms_config->root_path . $this->smiles_dir . '*.gif';

        $files = glob($pattern);

        if ($files) {
            foreach ($files as $file) {
                self::$smiles[pathinfo($file, PATHINFO_FILENAME)] = $this->cms_config->root . $this->smiles_dir . basename($file);
            }
        }

        $cache->set($cache_key, self::$smiles, 86400);

        return $this;
    }

    private function getSmiles() {
        return self::$smiles;
    }

}
