<?php
/*
 ======================================================================
 lastRSS 0.9.2

 Simple yet powerfull PHP class to parse RSS files.

 by Vojtech Semecky, webmaster @ webdot . cz

 Latest version, features, manual and examples:
 	http://lastrss.webdot.cz/

    modifed by https://instantcms.ru/

 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ======================================================================
*/

/**
* lastRSS
* Simple yet powerfull PHP class to parse RSS files.
*/
class lastRSS {

	public $default_cp  = 'UTF-8';
    public $CDATA       = 'content';
    public $cp          = '';
    public $rsscp       = '';
    public $items_limit = 0;
    public $stripHTML   = false;
    public $date_format = '';
    public $cache_dir   = '';
    public $cache_time  = 0;

    private $channeltags   = ['title', 'link', 'description', 'language', 'copyright', 'managingEditor', 'webMaster', 'lastBuildDate', 'rating', 'docs'];
    private $itemtags      = ['title', 'link', 'description', 'author', 'category', 'comments', 'enclosure', 'guid', 'pubDate', 'dc:date', 'source'];
    private $imagetags     = ['title', 'url', 'link', 'width', 'height'];
    private $textinputtags = ['title', 'description', 'name', 'link'];

    public function get($rss_url) {

        $result = $this->getCached($rss_url);

        if (!$result) {

            $result = $this->parse($rss_url);

            if ($result) {

                $this->cacheResult($rss_url, $result);

                $result['cached'] = 0;
            }
        }

        return $result;
    }

    private function getCached($rss_url) {

        if (!$this->cache_dir) {
            return false;
        }

        $cache_dir = $this->cache_dir . '/rsscache/';

        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777);
        }

        $cache_file = $cache_dir . md5($rss_url);

        if (!is_readable($cache_file)) {
            return false;
        }

        $timedif = (time() - filemtime($cache_file));

        if ($timedif < $this->cache_time) {

            $result = include $cache_file;

            if ($result) {

                $result['cached'] = 1;

                return $result;
            } else {
                unlink($cache_file);
            }
        } else {
            unlink($cache_file);
        }

        return false;
    }

    private function cacheResult($rss_url, $result) {

        if (!$this->cache_dir) {
            return false;
        }

        $cache_dir = $this->cache_dir . '/rsscache/';

        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777);
        }

        $cache_file = $cache_dir . md5($rss_url);

        file_put_contents($cache_file, '<?php return ' . var_export($result, true) . ';');

        return true;
    }

    /**
     * Modification of preg_match(); return trimed field with index 1
     * from 'classic' preg_match() array output
     *
     * @param string $pattern
     * @param string $subject
     * @return string
     */
	private function pregMatch($pattern, $subject) {

        // start regullar expression
        preg_match($pattern, $subject, $out);

        // if there is some result... process it and return it
        if (isset($out[1])) {

            // Process CDATA (if present)
            if ($this->CDATA == 'content') { // Get CDATA content (without CDATA tag)
                $out[1] = strtr($out[1], ['<![CDATA[' => '', ']]>' => '']);
            } elseif ($this->CDATA == 'strip') { // Strip CDATA
                $out[1] = strtr($out[1], ['<![CDATA[' => '', ']]>' => '']);
            }

            // If code page is set convert character encoding to required
            if ($this->cp != '') {
                $out[1] = iconv($this->rsscp, $this->cp . '//TRANSLIT', $out[1]);
            }

            return trim($out[1]);
        }

        // if there is NO result, return empty string
        return '';
    }

    /**
     * Replace HTML entities &something; by real characters
     *
     * @param string $string
     * @return string
     */
	private function unhtmlentities($string) {

        // Get HTML entities table
        $trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        // Flip keys<==>values
        $trans_tbl = array_flip($trans_tbl);
        // Add support for &apos; entity (missing in HTML_ENTITIES)
        $trans_tbl += ['&apos;' => "'"];
        // Replace entities by values
        return strtr($string, $trans_tbl);
    }

    /**
     * Parse() is private method used by get() to load and parse RSS file.
     *
     * @param string $rss_url
     * @return array
     */
	private function parse($rss_url) {

        $result = [];

        $rss_content = $this->getRssFromUrl($rss_url);
        if (!$rss_content) {
            return $result;
        }

        // Parse document encoding
        $result['encoding'] = $this->pregMatch("'encoding=[\'\"](.*?)[\'\"]'si", $rss_content);
        // if document codepage is specified, use it
        if ($result['encoding']) {
            $this->rsscp = $result['encoding'];
        } // This is used in pregMatch()
        // otherwise use the default codepage
        else {
            $this->rsscp = $this->default_cp;
        } // This is used in pregMatch()

        // Parse CHANNEL info
        $out_channel = [];
        preg_match("'<channel.*?>(.*)</channel>'si", $rss_content, $out_channel);
        if (!empty($out_channel[1])) {
            foreach ($this->channeltags as $channeltag) {
                $temp = $this->pregMatch("'<$channeltag.*?>(.*?)</$channeltag>'si", $out_channel[1]);
                if ($temp) {
                    $result[$channeltag] = $temp;
                } // Set only if not empty
            }
        }

        // If date_format is specified and lastBuildDate is valid
        if ($this->date_format && ($timestamp = strtotime($result['lastBuildDate'])) !== -1) {
            // convert lastBuildDate to specified date format
            $result['lastBuildDate'] = date($this->date_format, $timestamp);
        }

        // Parse TEXTINPUT info
        $out_textinfo = [];
        preg_match("'<textinput(|[^>]*[^/])>(.*?)</textinput>'si", $rss_content, $out_textinfo);
        // This a little strange regexp means:
        // Look for tag <textinput> with or without any attributes, but skip truncated version <textinput /> (it's not beggining tag)
        if (!empty($out_textinfo[2])) {
            foreach ($this->textinputtags as $textinputtag) {
                $temp = $this->pregMatch("'<$textinputtag.*?>(.*?)</$textinputtag>'si", $out_textinfo[2]);
                if ($temp) {
                    $result['textinput_' . $textinputtag] = $temp;
                } // Set only if not empty
            }
        }

        // Parse IMAGE info
        $out_imageinfo = [];
        preg_match("'<image.*?>(.*?)</image>'si", $rss_content, $out_imageinfo);
        if (!empty($out_imageinfo[1])) {
            foreach ($this->imagetags as $imagetag) {
                $temp = $this->pregMatch("'<$imagetag.*?>(.*?)</$imagetag>'si", $out_imageinfo[1]);
                if ($temp) {
                    $result['image_' . $imagetag] = $temp;
                } // Set only if not empty
            }
        }

        // Parse ITEMS

        $result['items'] = []; // create array even if there are no items

        $items = [];
        preg_match_all("'<item(| .*?)>(.*?)</item>'si", $rss_content, $items);

        if(!empty($items[2])){

            $i = 0;

            foreach ($items[2] as $rss_item) {
                // If number of items is lower then limit: Parse one item
                if ($i < $this->items_limit || !$this->items_limit) {

                    foreach ($this->itemtags as $itemtag) {
                        $temp = $this->pregMatch("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item);
                        if ($temp) {
                            $result['items'][$i][$itemtag] = $temp;
                        }
                    }

                    $temp = $this->pregMatch('#<enclosure url="([^"]+)#is', $rss_item);
                    if ($temp) {
                        $result['items'][$i]['enclosure'] = $temp;
                    }

                    // Strip HTML tags and other bullshit from DESCRIPTION
                    if ($this->stripHTML && !empty($result['items'][$i]['description'])) {
                        $result['items'][$i]['description'] = strip_tags(str_replace('><', '> <', $this->unhtmlentities(strip_tags(str_replace('><', '> <', $result['items'][$i]['description'])))));
                    }
                    // Strip HTML tags and other bullshit from TITLE
                    if ($this->stripHTML && $result['items'][$i]['title']) {
                        $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['title'])));
                    }
                    // If date_format is specified and pubDate is valid
                    if ($this->date_format && ($timestamp = strtotime($result['items'][$i]['pubDate'])) !== -1) {
                        // convert pubDate to specified date format
                        $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
                    }
                    // Item counter
                    $i++;
                }
            }

        }

        $result['items_count'] = $i;

        return $result;
    }

    private function getRssFromUrl($url) {

        if (function_exists('curl_init')) {

            $curl = curl_init();

            if (strpos($url, 'https') === 0) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            curl_setopt($curl, CURLOPT_USERAGENT, 'InstantCMS/'.cmsCore::getVersion().' ('.cmsConfig::get('language').')');
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);

            $data = curl_exec($curl);

            curl_close($curl);

        } else {

            $data = false;
        }

        return $data;
    }

}
