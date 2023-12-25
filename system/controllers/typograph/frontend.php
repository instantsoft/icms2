<?php

class typograph extends cmsFrontend {

    use icms\traits\oneable;

    /**
     * Массив смайлов
     * @var ?array
     */
    private static $smiles;
    /**
     * Директория смайлов
     * @var string
     */
    private $smiles_dir = 'static/smiles/';
    /**
     * Массив ссылок, которые не нужно заворачивать в редирект-ссылку
     * @var array
     */
    private $no_redirect_list = [];
    /**
     * Заворачивать внешние ссылки через редирект
     * @var ?boolean
     */
    private $build_redirect_link = null;
    /**
     * Автоматическая простановка тегов <br> на символ переноса строки
     * @var ?boolean
     */
    private $is_auto_br = null;
    /**
     * Пресет типографа
     * @var array
     */
    private $preset = [];

    /**
     * Экшен списка смайлов
     * @return string
     */
    public function actionGetSmiles() {
        return $this->cms_template->renderJSON([
            'smiles' => $this->loadSmiles()->getSmiles()
        ]);
    }

    /**
     * Заменяет составные символы смайлов на тег <img>
     * с соответствующей гифкой
     *
     * @param string $text
     * @return string
     */
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

    /**
     * Загружает в память все gif файлы из директории $smiles_dir
     * имя_файла -> URL
     *
     * @return $this
     */
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

    /**
     * Возвращает список смайлов
     * @return array
     */
    private function getSmiles() {
        return self::$smiles;
    }

    /**
     * Типографирует текст посредством Jevix
     *
     * @param string $text
     * @return string
     */
    public function parseText($text) {

        // Нет пресета по какой-то причине, убираем все теги
        if(!$this->preset){
            return strip_tags($text);
        }

        $errors = null;

        cmsCore::loadLib('jevix.class', 'Jevix');

        $jevix = new Jevix();

        // Протокол ссылок
        $jevix->cfgSetLinkProtocol($this->cms_config->protocol);

        // Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
        $jevix->cfgAllowTags($this->preset['options']['allowed_tags']);

        // После тега не нужно добавлять дополнительный <br/>
        $jevix->cfgSetTagBlockType([
            'p', 'li'
        ]);

        // Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort([
            'br', 'img', 'hr', 'input', 'source'
        ]);

        // Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent([
            'script', 'style', 'meta'
        ]);

        $jevix->cfgSetTagIsEmpty([
            'a', 'iframe', 'div', 'td'
        ]);

        // Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        foreach ($this->preset['options']['tags'] as $tag => $attrs) {

            if (!$attrs) {
                continue;
            }

            $params = [];

            foreach ($attrs as $attr) {

                if ($attr['params']) {
                    $attr['params'] = explode("\n", $attr['params']);
                    $attr['params'] = array_map(function($val){ return trim($val); }, $attr['params']);
                } else {
                    $attr['params'] = [];
                }

                switch ($attr['type']) {
                    case '#text':
                    case '#int':
                    case '#link':
                    case '#image':
                        $params[$attr['name']] = $attr['type'];
                        break;
                    case '#domain':
                        $params[$attr['name']] = ['#domain' => $attr['params']];
                        break;
                    case '#array':
                        $params[$attr['name']] = $attr['params'];
                        break;
                }
            }

            $jevix->cfgAllowTagParams($tag, $params);
        }

        // Устанавливаем параметры тегов являющиеся обязательными. Без них вырезает тег оставляя содержимое.
        $jevix->cfgSetTagParamsRequired('img', 'src');
        $jevix->cfgSetTagParamsRequired('a', 'href');
        $jevix->cfgSetTagParamsRequired('iframe', 'src');
        $jevix->cfgSetTagParamsRequired('source', 'src');

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
        $jevix->cfgSetTagChilds('picture', ['source', 'img'], true);

        // Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted([
            'code'
        ]);

        // Устанавливаем автозамену
        if($this->preset['options']['autoreplace']){

            $search = []; $replace = [];

            foreach ($this->preset['options']['autoreplace'] as $autoreplace) {
                $search[] = $autoreplace['search'];
                $replace[] = $autoreplace['replace'];
            }

            $jevix->cfgSetAutoReplace($search, $replace);
        }

        // включаем режим замены переноса строк на тег <br/>
        $jevix->cfgSetAutoBrMode($this->preset['options']['is_auto_br']);

        // включаем режим автоматического определения ссылок
        $jevix->cfgSetAutoLinkMode($this->preset['options']['is_auto_link_mode']);

        // Теги, внутри которых не менять переносы на <br>
        $jevix->cfgSetTagNoAutoBr(['ul', 'ol', 'code', 'pre', 'video', 'table', 'tr', 'tbody', 'thead']);

        // Надо ли обрабатывать колбэки
        if($this->preset['options']['is_process_callback']){

            // Добавляем pre тег
            $jevix->cfgSetTagPreformatted([
                'pre'
            ]);

            foreach ($this->preset['options']['callback'] as $tag => $callback) {
                if ($callback) {

                    $controller = $this;
                    $method = null;

                    $callback_param = explode('|', $callback);

                    // Только имя метода
                    if(count($callback_param) === 1){
                        $method = $callback_param[0];
                    } else {
                        if($callback_param[0] !== 'typograph'){
                            $controller = cmsCore::getController($callback_param[0]);
                        }
                        $method = $callback_param[1];
                    }

                    if(method_exists($controller, $method)){
                        $jevix->cfgSetTagCallbackFull($tag, [$controller, $method]);
                    }
                }
            }
        }

        // Библиотека может вызвать исключение
        try {
            $text = $jevix->parse($text, $errors);
        } catch (Exception $exc) {
            // Убираем всё, что-то пошло не так
            $text = strip_tags($text);
        }

        return $text;
    }

    /**
     * Подготоваливает параметры и текст для Jevix
     *
     * @param array|string $data
     * @return string Текст для обработки
     */
    public function preparePresetParamsAndGetText($data) {

        $build_smiles = false;
        $is_process_callback = null;

        if (is_array($data)) {

            $text = $data['text'];

            if (isset($data['is_auto_br'])) {
                $this->is_auto_br = $data['is_auto_br'];
            }

            if (isset($data['build_redirect_link'])) {
                $this->build_redirect_link = $data['build_redirect_link'];
            }

            if (isset($data['is_process_callback'])) {
                $is_process_callback = $data['is_process_callback'];
            }

            $typograph_id = $data['typograph_id'] ?? 1;

        } else {

            $typograph_id = 1;

            $text = $data;
        }

        $this->preset = $this->getOnce($this->model)->getPreset($typograph_id);

        if (!cmsController::enabled('redirect')) {
            $this->build_redirect_link = false;
        }

        // Перезаписываем опции, если были заданы
        if ($this->preset) {

            if ($is_process_callback !== null) {
                $this->preset['options']['is_process_callback'] = $is_process_callback;
            }

            if ($this->is_auto_br !== null) {
                $this->preset['options']['is_auto_br'] = $this->is_auto_br;
            }

            if ($this->build_redirect_link !== null) {
                $this->preset['options']['build_redirect_link'] = $this->build_redirect_link;
            }

            // Только если обрабатываем колбэки
            if(!empty($this->preset['options']['build_smiles']) && $this->preset['options']['is_process_callback']) {
                $build_smiles = true;
            }

            if($this->preset['options']['build_redirect_link']) {

                $redirect_options = cmsController::loadOptions('redirect');

                if(!empty($redirect_options['no_redirect_list'])){
                    $no_redirect_list = explode("\n", $redirect_options['no_redirect_list']);
                    $this->no_redirect_list = array_map(function($val){ return trim($val); }, $no_redirect_list);
                }
            }
        }

        if ($build_smiles) {
            $text = $this->replaceEmotionToSmile($text);
        }

        return $text;
    }

    /**
     * Колбэк Jevix для тега <a>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    public function linkRedirectPrefix($tag, $params, $content) {

        $href_params = parse_url($params['href']);

        $is_external_link = !empty($href_params['host']) && strpos($params['href'], $this->cms_config->host) !== 0;

        if ($is_external_link) {

            $params['class']  = (isset($params['class']) ? $params['class'] . ' external_link' : 'external_link');
            $params['target'] = '_blank';

            if ($this->preset['options']['build_redirect_link']) {

                $build_redirect_link = true;

                if($this->no_redirect_list) {

                    $host = parse_url($params['href'], PHP_URL_HOST);

                    if(in_array($host, $this->no_redirect_list, true)){
                        $build_redirect_link = false;
                    }
                }

                if($build_redirect_link) {
                    $params['href'] = href_to('redirect') . '?url=' . urlencode($params['href']);
                }
            }
        }

        $tag_string = '<a';

        foreach ($params as $param => $value) {
            if ($value) {
                $tag_string .= ' ' . $param . '="' . $value . '"';
            }
        }

        $tag_string .= '>' . $content . '</a>';

        return $tag_string;
    }

    /**
     * Колбэк Jevix для тега <img>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
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
            if ($value) {
                $tag_string .= ' ' . $param . '="' . $value . '"';
            }
        }

        $tag_string .= '>';

        if (!empty($params['width'])) {
            $tag_string = '<a href="' . $params['src'] . '" class="icms-image__modal ajax-modal d-inline-block" style="max-width:' . $params['width'] . 'px">' . $tag_string . '</a>';
        }

        return $tag_string;
    }

    /**
     * Колбэк Jevix для тега <spoiler>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    public function parseSpoiler($tag, $params, $content) {

        if (empty($content)) {
            return '';
        }

        $id = string_random();
        $title = !empty($params['title']) ? $params['title'] : '';

        return '<div class="spoiler"><input tabindex="-1" type="checkbox" id="' . $id . '"><label for="' . $id . '">' . $title . '</label><div class="spoiler_body">' . $content . '</div></div>';
    }

    /**
     * Колбэк Jevix для тега <iframe>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    public function parseIframe($tag, $params, $content) {

        if (empty($params['src'])) {
            return '';
        }

        return $this->getVideoCode($params['src']);
    }

    /**
     * Колбэк Jevix для тега <facebook>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
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

    /**
     * Колбэк Jevix для тега <youtube>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
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

    /**
     * Колбэк Jevix для тега <code>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    public function parseCode($tag, $params, $content) {

        cmsCore::loadLib('geshi/geshi', 'GeSHi');

        $geshi = new GeSHi(htmlspecialchars_decode($content), (isset($params['type']) ? $params['type'] : 'php'));
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        return '<div class="bb_tag_code">' . $geshi->parse_code() . '</div>';
    }

    /**
     * Колбэк Jevix для тега <pre>
     *
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    public function parsePre($tag, $params, $content) {

        $content = htmlspecialchars_decode($content);
        $content = preg_replace('#^<code>(.*)<\/code>$#uis', '$1', $content);

        cmsCore::loadLib('geshi/geshi', 'GeSHi');

        $geshi = new GeSHi($content, (isset($params['class']) ? str_replace('language-', '', $params['class']) : 'php'));
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

        return '<div class="bb_tag_code">' . $geshi->parse_code() . '</div>';
    }

}
