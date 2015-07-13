<?php

class onTypographHtmlFilter extends cmsAction {

    public function run($data){

        $errors = null;
        $is_auto_br = true;
        
        if (is_array($data)){
            $text = $data['text'];
            $is_auto_br = $data['is_auto_br'];
        } else {
            $text = $data;
        }
        
        $text = $this->getJevix($is_auto_br)->parse($text, $errors);

//        dump($text);
        
        return $text;

    }

    private function getJevix($is_auto_br){

        cmsCore::loadLib('jevix.class');

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
            'video', 'audio', 'youtube',
            'object', 'param', 'embed', 'iframe'
        ));

        // Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort(array(
            'br', 'img', 'hr',
        ));

        // Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted(array(
            'code','pre'
        ));

        // Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent(array(
            'script', 'style', 'meta'
        ));
        
        $jevix->cfgSetTagIsEmpty(array(
            'iframe', 'param'
        ));

        // Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        $jevix->cfgAllowTagParams('a', array('href', 'name' => '#text'));
        $jevix->cfgAllowTagParams('img', array('src', 'style', 'alt' => '#text', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int'));
        $jevix->cfgAllowTagParams('span', array('style'));
        $jevix->cfgAllowTagParams('object', array('width' => '#int', 'height' => '#int', 'data' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com')), 'type' => '#text'));
        $jevix->cfgAllowTagParams('param', array('name' => '#text', 'value' => '#text'));
        $jevix->cfgAllowTagParams('embed', array('src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com')), 'type' => '#text','allowscriptaccess' => '#text', 'allowfullscreen' => '#text','width' => '#int', 'height' => '#int', 'flashvars'=> '#text', 'wmode'=> '#text'));
        $jevix->cfgAllowTagParams('iframe', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','vk.com'))));
        $jevix->cfgAllowTagParams('table', array('width' => '#int', 'height' => '#int', 'cellpadding' => '#int', 'cellspacing' => '#int', 'border' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text'));
        $jevix->cfgAllowTagParams('td', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text', 'colspan'=>'#int', 'rowspan'=>'#int'));
        $jevix->cfgAllowTagParams('th', array('width' => '#int', 'height' => '#int', 'style' => '#text', 'align'=>'#text', 'valign'=>'#text', 'colspan'=>'#int', 'rowspan'=>'#int'));
        $jevix->cfgAllowTagParams('p', array('style'));
        $jevix->cfgAllowTagParams('div', array('style'));

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

        // Отключаем типографирование в определенном теге
        $jevix->cfgSetTagNoTypography('code','pre','youtube', 'iframe');

        // Ставим колбэк для youtube
        $jevix->cfgSetTagCallback('youtube', array($this, 'parseYouTubeVideo'));

        return $jevix;

    }

    public function parseYouTubeVideo($content){

        $video_id = $this->parseYouTubeVideoID(trim($content));

        if (!$video_id) { return false; }

        $code = '<iframe width="320" height="240" src="http://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';

        return $code;

    }

    private function parseYouTubeVideoID($url) {

        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
        
    }

}
