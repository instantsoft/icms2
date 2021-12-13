<?php

class search extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    protected $unknown_action_as_index_param = true;

    public function parseHashTag($text) {

        if (!$text) {
            return $text;
        }

        if (!empty($this->options['types']) &&
                !in_array($this->cms_core->controller, $this->options['types'])) {
            $link = href_to('search');
        } else {
            $link = href_to('search', $this->cms_core->controller);
        }

        if (preg_match_all('/\B#([а-яёa-z]{1}[а-яёa-z0-9\-_]{3,19})/ui', $text, $match) && !empty($match[1])) {
            foreach ($match[1] as $hash_tag) {
                $text = str_replace('#' . $hash_tag, '<a class="hashtag" href="' . $link . '?q=' . urlencode('#' . $hash_tag) . '">#' . $hash_tag . '</a>', $text);
            }
        }

        return $text;
    }

}
