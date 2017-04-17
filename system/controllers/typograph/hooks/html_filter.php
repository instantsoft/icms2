<?php

class onTypographHtmlFilter extends cmsAction {

    public function run($data){

        $errors              = null;
        $is_auto_br          = true;
        $build_redirect_link = true;
        $build_smiles        = true;

        if (is_array($data)){
            $text                = $data['text'];
            $is_auto_br          = $data['is_auto_br'];
            if(isset($data['build_redirect_link'])){
                $build_redirect_link = $data['build_redirect_link'];
            }
            if(isset($data['build_smiles'])){
                $build_smiles = $data['build_smiles'];
            }
        } else {
            $text = $data;
        }

        $text = $this->getJevix($is_auto_br, $build_redirect_link)->parse($text, $errors);

        if($build_smiles){
            $text = $this->replaceEmotionToSmile($text);
        }

        return $text;

    }

    private function getJevix($is_auto_br, $build_redirect_link){

        cmsCore::loadLib('jevix.class', 'Jevix');

        $jevix = new Jevix();

        // Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
        $jevix->cfgAllowTags(array(
            'p', 'br', 'span', 'div',
            'a', 'img',
            'b', 'i', 'u', 's', 'del', 'em', 'strong', 'sup', 'sub', 'hr', 'font',
            'ul', 'ol', 'li',
            'table', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th',
            'h1','h2','h3','h4','h5','h6',
            'pre', 'code', 'blockquote',
            'video', 'audio', 'youtube','facebook',
            'object', 'param', 'embed', 'iframe','spoiler'
        ));

        // Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort(array(
            'br', 'img', 'hr', 'embed'
        ));

        // Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted(array(
            'pre', 'video'
        ));

        // Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent(array(
            'script', 'style', 'meta'
        ));

        $jevix->cfgSetTagIsEmpty(array(
            'param','embed','a','iframe','div'
        ));

        // Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        $jevix->cfgAllowTagParams('a', array('href' => '#link', 'name' => '#text', 'target' => '#text', 'class' => '#text'));
        $jevix->cfgAllowTagParams('img', array('src', 'style' => '#text', 'alt' => '#text', 'title' => '#text', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int', 'class' => '#text'));
        $jevix->cfgAllowTagParams('span', array('style' => '#text'));
        $jevix->cfgAllowTagParams('object', array('width' => '#int', 'height' => '#int', 'data' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com')), 'type' => '#text'));
        $jevix->cfgAllowTagParams('param', array('name' => '#text', 'value' => '#text'));
        $jevix->cfgAllowTagParams('embed', array('src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com')), 'type' => '#text','allowscriptaccess' => '#text', 'allowfullscreen' => '#text','width' => '#int', 'height' => '#int', 'flashvars'=> '#text', 'wmode'=> '#text'));
        $jevix->cfgAllowTagParams('iframe', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'frameborder' => '#int', 'allowfullscreen' => '#text', 'src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com','my.mail.ru','facebook.com'))));
        $jevix->cfgAllowTagParams('table', array('width' => '#int', 'height' => '#int', 'cellpadding' => '#int', 'cellspacing' => '#int', 'border' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text'));
        $jevix->cfgAllowTagParams('td', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text', 'colspan'=>'#int', 'rowspan'=>'#int'));
        $jevix->cfgAllowTagParams('th', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text', 'colspan'=>'#int', 'rowspan'=>'#int'));
        $jevix->cfgAllowTagParams('p', array('style' => '#text'));
        $jevix->cfgAllowTagParams('div', array('style' => '#text', 'class' => '#text'));
        $jevix->cfgAllowTagParams('spoiler', array('title' => '#text'));
        $jevix->cfgAllowTagParams('code', array('type' => '#text'));

        // Устанавливаем параметры тегов являющиеся обязательными. Без них вырезает тег оставляя содержимое.
        $jevix->cfgSetTagParamsRequired('img', 'src');
        $jevix->cfgSetTagParamsRequired('a', 'href');

        // Устанавливаем теги которые может содержать тег контейнер
        $jevix->cfgSetTagChilds('ul',array('li'),false,true);
        $jevix->cfgSetTagChilds('ol',array('li'),false,true);
        $jevix->cfgSetTagChilds('table',array('tr', 'tbody', 'thead', 'tfoot', 'th', 'td'),false,true);
        $jevix->cfgSetTagChilds('tbody',array('tr', 'td', 'th'),false,true);
        $jevix->cfgSetTagChilds('thead',array('tr', 'td', 'th'),false,true);
        $jevix->cfgSetTagChilds('tfoot',array('tr', 'td', 'th'),false,true);
        $jevix->cfgSetTagChilds('tr',array('td'),false,true);
        $jevix->cfgSetTagChilds('tr',array('th'),false,true);

        // Устанавливаем автозамену
        $jevix->cfgSetAutoReplace(array('+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)'), array('±', '©', '©', '®', '©', '©', '®'));

        // включаем режим замены переноса строк на тег <br/>
        $jevix->cfgSetAutoBrMode($is_auto_br);

        // включаем режим автоматического определения ссылок
        $jevix->cfgSetAutoLinkMode(true);

        // если нужно обрабатывать внешние ссылки в редирект
        if($build_redirect_link){
            $jevix->cfgSetTagCallbackFull('a', array($this, 'linkRedirectPrefix'));
        }

        // Отключаем типографирование в определенном теге
        $jevix->cfgSetTagNoTypography('pre','youtube', 'iframe');

        // Ставим колбэк для youtube
        $jevix->cfgSetTagCallbackFull('youtube', array($this, 'parseYouTubeVideo'));

        // Ставим колбэк для facebook
        $jevix->cfgSetTagCallbackFull('facebook', array($this, 'parseFacebookVideo'));

        // Ставим колбэк на iframe
        $jevix->cfgSetTagCallbackFull('iframe', array($this, 'parseIframe'));

        // Ставим колбэк для кода
        $jevix->cfgSetTagCallbackFull('code', array($this, 'parseCode'));

        // Ставим колбэк для спойлеров
        $jevix->cfgSetTagCallbackFull('spoiler', array($this, 'parseSpoiler'));

        return $jevix;

    }

    public function linkRedirectPrefix($tag, $params, $content) {

        $href_params = parse_url($params['href']);

        $is_external_link = !empty($href_params['host']) && !strstr($params['href'], parse_url($this->cms_config->host, PHP_URL_HOST));

        if($is_external_link){
            $params['class']  = (isset($params['class']) ? $params['class'].' external_link' : 'external_link');
            $params['target'] = '_blank';
            $params['href']   = href_to('redirect').'?url='.urlencode($params['href']);
            $params['rel']    = 'nofollow';
        }

        $tag_string = '<a';

        foreach($params as $param => $value) {
            if ($value != '') {
                $tag_string.=' '.$param.'="'.$value.'"';
            }
        }

        $tag_string .= '>'.$content.'</a>';

        return $tag_string;

    }

    public function parseSpoiler($tag, $params, $content) {

        if(empty($content)){
            return '';
        }

        $id = uniqid();
        $title = !empty($params['title']) ? htmlspecialchars($params['title']) : '';

        return '<div class="spoiler"><input tabindex="-1" type="checkbox" id="'.$id.'"><label for="'.$id.'">'.$title.'</label><div class="spoiler_body">'.$content.'</div></div>';

    }

    public function parseIframe($tag, $params, $content) {

        if(empty($params['src'])){
            return '';
        }

        return $this->getVideoCode($params['src']);

    }

    public function parseFacebookVideo($tag, $params, $content){

        $video_link = (trim(strip_tags($content)));

        $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:facebook\.com(?:/[^\/]+/videos/|/video\.php\?v=))([0-9]+)(?:.+)?$#x';
        preg_match($pattern, $video_link, $matches);

        if(empty($matches[1])){
            $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:facebook\.com(?:/[^\/]+/videos/[^\/]+))/([0-9]+)(?:.+)?$#x';
            preg_match($pattern, $video_link, $matches);
        }

        if(empty($matches[1])){
            return '';
        }

        return $this->getVideoCode('https://www.facebook.com/video/embed?video_id='.$matches[1]);

    }

    public function parseYouTubeVideo($tag, $params, $content){

        $video_id = $this->parseYouTubeVideoID(trim(strip_tags($content)));

        return $this->getVideoCode('//www.youtube.com/embed/'.$video_id);

    }

    private function getVideoCode($src) {
        return '<div class="video_wrap"><iframe class="video_frame" src="'.$src.'" frameborder="0" allowfullscreen></iframe></div>';
    }

    private function parseYouTubeVideoID($url) {

        $pattern = '#^(?:(?:https|http)?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;

    }

    public function parseCode($tag, $params, $content){

        cmsCore::loadLib('geshi/geshi', 'GeSHi');

        $geshi = new GeSHi(trim(str_replace('<br/>', '', $content)), (isset($params['type']) ? $params['type'] : 'php'));

        return $geshi->parse_code();

    }

}
