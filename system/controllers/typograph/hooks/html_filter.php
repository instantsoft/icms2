<?php

class onTypographHtmlFilter extends cmsAction {

    private $build_redirect_link = true;
    private $is_auto_br = true;

    public function run($data) {

        $errors = null;
        $build_smiles = true;

        if (is_array($data)) {

            $text = $data['text'];

            if (isset($data['is_auto_br'])) {
                $this->is_auto_br = $data['is_auto_br'];
            }

            if (isset($data['build_redirect_link'])) {
                $this->build_redirect_link = $data['build_redirect_link'];
            }

            if (isset($data['build_smiles'])) {
                $build_smiles = $data['build_smiles'];
            }
        } else {
            $text = $data;
        }

        if (!cmsController::enabled('redirect')) {
            $this->build_redirect_link = false;
        }

        $text = $this->getJevix()->parse($text, $errors);

        if ($build_smiles) {
            $text = $this->replaceEmotionToSmile($text);
        }

        return $text;
    }

    private function getJevix() {

        cmsCore::loadLib('jevix.class', 'Jevix');

        $jevix = new Jevix();

        // Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
        $jevix->cfgAllowTags([
            'p', 'br', 'span', 'div',
            'a', 'img', 'input', 'label',
            'b', 'i', 'u', 's', 'del', 'em', 'strong', 'sup', 'sub', 'hr', 'font',
            'ul', 'ol', 'li',
            'table', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'pre', 'code', 'blockquote', 'picture',
            'video', 'source', 'audio', 'youtube', 'facebook', 'figure', 'figcaption',
            'iframe', 'spoiler', 'cite', 'footer', 'address'
        ]);

        // Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort([
            'br', 'img', 'hr', 'input', 'source'
        ]);

        // Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted(array(
            'pre', 'code'
        ));

        // Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent([
            'script', 'style', 'meta'
        ]);

        $jevix->cfgSetTagIsEmpty([
            'a', 'iframe', 'div'
        ]);

        // Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        $jevix->cfgAllowTagParams('address', ['class' => '#text']);
        $jevix->cfgAllowTagParams('a', ['href' => '#link', 'name' => '#text', 'target' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('img', ['src', 'style' => '#text', 'alt' => '#text', 'title' => '#text', 'align' => ['right', 'left', 'center'], 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int', 'class' => '#text']);
        $jevix->cfgAllowTagParams('span', ['style' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('em', ['class' => '#text']);
        $jevix->cfgAllowTagParams('input', ['tabindex' => '#text', 'type' => '#text', 'id' => '#text']);
        $jevix->cfgAllowTagParams('label', ['class' => '#text', 'for' => '#text']);
        $jevix->cfgAllowTagParams('footer', ['class' => '#text']);
        $jevix->cfgAllowTagParams('iframe', ['width' => '#int', 'height' => '#int', 'style' => '#text', 'frameborder' => '#int', 'allowfullscreen' => '#text', 'src' => ['#domain' => ['youtube.com', 'yandex.ru', 'rutube.ru', 'vimeo.com', 'vk.com', 'my.mail.ru', 'facebook.com', parse_url($this->cms_config->host, PHP_URL_HOST)]]]);
        $jevix->cfgAllowTagParams('table', ['width' => '#int', 'height' => '#int', 'cellpadding' => '#int', 'cellspacing' => '#int', 'border' => '#int', 'style' => '#text', 'align' => '#text', 'valign' => '#text']);
        $jevix->cfgAllowTagParams('td', ['width' => '#int', 'height' => '#int', 'style' => '#text', 'align' => '#text', 'valign' => '#text', 'colspan' => '#int', 'rowspan' => '#int']);
        $jevix->cfgAllowTagParams('th', ['width' => '#int', 'height' => '#int', 'style' => '#text', 'align' => '#text', 'valign' => '#text', 'colspan' => '#int', 'rowspan' => '#int']);
        $jevix->cfgAllowTagParams('p', ['style' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('div', ['style' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('spoiler', ['title' => '#text']);
        $jevix->cfgAllowTagParams('code', ['type' => '#text']);
        $jevix->cfgAllowTagParams('pre', ['class' => '#text']);
        $jevix->cfgAllowTagParams('figure', ['style' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('figcaption', ['style' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('h2', ['id' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('h3', ['id' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('h4', ['id' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('h5', ['id' => '#text', 'class' => '#text']);
        $jevix->cfgAllowTagParams('video', ['controls' => '#text', 'class' => '#text', 'width' => '#int', 'height' => '#int']);
        $jevix->cfgAllowTagParams('audio', ['controls' => '#text', 'class' => '#text', 'src' => '#text', 'autoplay' => '#text', 'preload' => '#text']);
        $jevix->cfgAllowTagParams('source', ['src' => '#text', 'type' => '#text', 'media' => '#text']);

        // Устанавливаем параметры тегов являющиеся обязательными. Без них вырезает тег оставляя содержимое.
        $jevix->cfgSetTagParamsRequired('img', 'src');
        $jevix->cfgSetTagParamsRequired('a', 'href');

        // Устанавливаем теги которые может содержать тег контейнер
        $jevix->cfgSetTagChilds('video', ['source'], false, true);
        $jevix->cfgSetTagChilds('ul', ['li'], false, true);
        $jevix->cfgSetTagChilds('ol', ['li'], false, true);
        $jevix->cfgSetTagChilds('table', ['tr', 'tbody', 'thead', 'tfoot', 'th', 'td'], false, true);
        $jevix->cfgSetTagChilds('tbody', ['tr', 'td', 'th'], false, true);
        $jevix->cfgSetTagChilds('thead', ['tr', 'td', 'th'], false, true);
        $jevix->cfgSetTagChilds('tfoot', ['tr', 'td', 'th'], false, true);
        $jevix->cfgSetTagChilds('tr', ['td'], false, true);
        $jevix->cfgSetTagChilds('tr', ['th'], false, true);

        // Устанавливаем автозамену
        $jevix->cfgSetAutoReplace(['+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)'], ['±', '©', '©', '®', '©', '©', '®']);

        // включаем режим замены переноса строк на тег <br/>
        $jevix->cfgSetAutoBrMode($this->is_auto_br);

        // включаем режим автоматического определения ссылок
        $jevix->cfgSetAutoLinkMode(true);

        // обрабатываем внешние ссылки
        $jevix->cfgSetTagCallbackFull('a', [$this, 'linkRedirectPrefix']);

        $jevix->cfgSetTagCallbackFull('img', [$this, 'parseImg']);

        // Отключаем типографирование в определенном теге
        $jevix->cfgSetTagNoTypography(['pre', 'youtube', 'iframe', 'code']);

        $jevix->cfgSetTagNoAutoBr(['ul', 'ol', 'code', 'video']);

        // Ставим колбэк для youtube
        $jevix->cfgSetTagCallbackFull('youtube', [$this, 'parseYouTubeVideo']);

        // Ставим колбэк для facebook
        $jevix->cfgSetTagCallbackFull('facebook', [$this, 'parseFacebookVideo']);

        // Ставим колбэк на iframe
        $jevix->cfgSetTagCallbackFull('iframe', [$this, 'parseIframe']);

        // Ставим колбэк для кода
        $jevix->cfgSetTagCallbackFull('code', [$this, 'parseCode']);
        $jevix->cfgSetTagCallbackFull('pre', [$this, 'parsePre']);

        // Ставим колбэк для спойлеров
        $jevix->cfgSetTagCallbackFull('spoiler', [$this, 'parseSpoiler']);

        return $jevix;
    }

    public function linkRedirectPrefix($tag, $params, $content) {

        $href_params = parse_url($params['href']);

        $is_external_link = !empty($href_params['host']) && strpos($params['href'], $this->cms_config->host) !== 0;

        if ($is_external_link) {

            $params['class']  = (isset($params['class']) ? $params['class'] . ' external_link' : 'external_link');
            $params['target'] = '_blank';

            if ($this->build_redirect_link) {
                $params['href'] = href_to('redirect') . '?url=' . urlencode($params['href']);
            }
        }

        $tag_string = '<a';

        foreach ($params as $param => $value) {
            if ($value != '') {
                $tag_string .= ' ' . $param . '="' . $value . '"';
            }
        }

        $tag_string .= '>' . $content . '</a>';

        return $tag_string;
    }

    public function parseImg($tag, $params, $content) {

        if (!empty($params['style'])) {

            $styles = explode(';', rtrim(trim($params['style']), ';'));

            foreach ($styles as $k => $style) {

                list($css_name, $css_value) = explode(':', $style);

                if (trim($css_name) == 'height') {
                    unset($styles[$k]);
                }
            }

            $params['style'] = implode(';', $styles);
        }

        if (!in_array('alt', array_keys($params))) {
            $params['alt'] = LANG_PHOTO;
        }

        $tag_string = '<img';

        foreach ($params as $param => $value) {
            if (in_array($param, ['height'])) {
                continue;
            }
            if ($value != '') {
                $tag_string .= ' ' . $param . '="' . $value . '"';
            }
        }

        $tag_string .= '>';

        if (!empty($params['width'])) {
            $tag_string = '<a href="' . $params['src'] . '" class="icms-image__modal ajax-modal d-inline-block" style="max-width:' . $params['width'] . 'px">' . $tag_string . '</a>';
        }

        return $tag_string;
    }

    public function parseSpoiler($tag, $params, $content) {

        if (empty($content)) {
            return '';
        }

        $id = string_random();
        $title = !empty($params['title']) ? $params['title'] : '';

        return '<div class="spoiler"><input tabindex="-1" type="checkbox" id="' . $id . '"><label for="' . $id . '">' . $title . '</label><div class="spoiler_body">' . $content . '</div></div>';
    }

    public function parseIframe($tag, $params, $content) {

        if (empty($params['src'])) {
            return '';
        }

        return $this->getVideoCode($params['src']);
    }

    public function parseFacebookVideo($tag, $params, $content) {

        $video_link = (trim(strip_tags($content)));

        $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:facebook\.com(?:/[^\/]+/videos/|/video\.php\?v=))([0-9]+)(?:.+)?$#x';
        preg_match($pattern, $video_link, $matches);

        if (empty($matches[1])) {
            $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:facebook\.com(?:/[^\/]+/videos/[^\/]+))/([0-9]+)(?:.+)?$#x';
            preg_match($pattern, $video_link, $matches);
        }

        if (empty($matches[1])) {
            return '';
        }

        return $this->getVideoCode('https://www.facebook.com/video/embed?video_id=' . $matches[1]);
    }

    public function parseYouTubeVideo($tag, $params, $content) {

        $video_id = $this->parseYouTubeVideoID(trim(strip_tags($content)));

        return $this->getVideoCode('//www.youtube.com/embed/' . $video_id);
    }

    private function getVideoCode($src) {
        return '<div class="video_wrap embed-responsive embed-responsive-16by9"><iframe class="video_frame embed-responsive-item" src="' . $src . '" frameborder="0" allowfullscreen></iframe></div>';
    }

    private function parseYouTubeVideoID($url) {

        $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
        preg_match($pattern, $url, $matches);

        return (isset($matches[1])) ? $matches[1] : false;
    }

    public function parseCode($tag, $params, $content) {

        cmsCore::loadLib('geshi/geshi', 'GeSHi');

        $geshi = new GeSHi(htmlspecialchars_decode($content), (isset($params['type']) ? $params['type'] : 'php'));
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        return '<div class="bb_tag_code">' . $geshi->parse_code() . '</div>';
    }

    public function parsePre($tag, $params, $content) {

        $content = htmlspecialchars_decode($content);
        $content = preg_replace('#^<code>(.*)<\/code>$#uis', '$1', $content);

        cmsCore::loadLib('geshi/geshi', 'GeSHi');

        $geshi = new GeSHi($content, (isset($params['class']) ? str_replace('language-', '', $params['class']) : 'php'));
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        return '<div class="bb_tag_code">' . $geshi->parse_code() . '</div>';
    }

}
