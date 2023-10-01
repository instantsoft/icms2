<?php
/**
 * Класс пагинации
 *
 * идея @jasongrimes/php-paginator
 *
 */
class cmsPaginator {

    private $total;
    private $pages_count;
    private $perpage;
    private $current_page;
    private $base_uri;
    private $anchor = '';
    private $page_param_name = 'page';
    private $query = [];

    private $max_show_pages = 5;

    public function __construct($total, $perpage, $current_page, $base_uri, $query = []) {

        $this->total        = (int)$total;
        $this->perpage      = (int)$perpage;
        $this->current_page = (int)$current_page;

        $this->updatePagesCount();

        if ($this->pages_count > 1) {
            $this->setQuery($query)->setBaseUri($base_uri);
        }
    }

    public function setMaxPagesToShow($max_show_pages) {
        $this->max_show_pages = $max_show_pages; return $this;
    }

    public function setPageParamName($page_param_name) {
        $this->page_param_name = $page_param_name;
        return $this;
    }

    public function setQuery($query) {

        if (!is_array($query)){
            parse_str($query, $query);
        }

        $this->query = $query;

        return $this;
    }

    public function setBaseUri($base_uri) {

        if (is_string($base_uri) && strpos($base_uri, '#') !== false){
            list($base_uri, $this->anchor) = explode('#', $base_uri);
        }

        if (!$base_uri) { $base_uri = cmsCore::getInstance()->uri_absolute; }

        if (!is_array($base_uri)){
            $base_uri = array(
                'first' => $base_uri,
                'base'  => $base_uri
            );
        } elseif(!isset($base_uri['first'])){
            $base_uri['first'] = $base_uri['base'];
        }

        $this->base_uri = $base_uri;

        return $this;
    }

    private function updatePagesCount() {
        $this->pages_count = ($this->perpage == 0 ? 0 : intval(ceil($this->total / $this->perpage)));
        return $this;
    }

    private function getPageUrl($page) {

        if(!$page){ return null; }

        $this->query[$this->page_param_name] = $page;

        $uri = $this->query[$this->page_param_name] == 1 ? $this->base_uri['first'] : $this->base_uri['base'];

        $sep = strpos($uri, '?') !== false ? '&' : '?';

        if ($this->query[$this->page_param_name] == 1) { unset($this->query[$this->page_param_name]); }

        return $uri . ($this->query ? $sep .http_build_query($this->query) : '') . ($this->anchor ? '#'.$this->anchor : '');
    }

    private function getNextPage() {
        if ($this->current_page < $this->pages_count) {
            return $this->current_page + 1;
        }
        return null;
    }

    private function getPrevPage() {
        if ($this->current_page > 1) {
            return $this->current_page - 1;
        }
        return null;
    }

    private function getNextUrl() {
        return $this->getPageUrl($this->getNextPage());
    }

    private function getPrevUrl() {
        return $this->getPageUrl($this->getPrevPage());
    }

    private function getPages() {

        $pages = [];

        if ($this->pages_count <= 1) {
            return $pages;
        }

        if ($this->pages_count <= $this->max_show_pages) {
            for ($i = 1; $i <= $this->pages_count; $i++) {
                $pages[] = $this->createPage($i, $i == $this->current_page);
            }
        } else {

            $num_adjacents = floor(($this->max_show_pages - 3) / 2);

            if ($this->current_page + $num_adjacents > $this->pages_count) {
                $sliding_start = $this->pages_count - $this->max_show_pages + 2;
            } else {
                $sliding_start = $this->current_page - $num_adjacents;
            }
            if ($sliding_start < 2){
                $sliding_start = 2;
            }

            $sliding_end = $sliding_start + $this->max_show_pages - 3;
            if ($sliding_end >= $this->pages_count){
                $sliding_end = $this->pages_count - 1;
            }

            $pages[] = $this->createPage(1, $this->current_page == 1);

            if ($sliding_start > 2) {
                $pages[] = $this->createPageEllipsis();
            }

            for ($i = $sliding_start; $i <= $sliding_end; $i++) {
                $pages[] = $this->createPage($i, $i == $this->current_page);
            }

            if ($sliding_end < $this->pages_count - 1) {
                $pages[] = $this->createPageEllipsis();
            }

            $pages[] = $this->createPage($this->pages_count, $this->current_page == $this->pages_count);
        }


        return $pages;
    }

    private function createPage($page, $is_current = false) {
        return [
            'num'        => $page,
            'url'        => $this->getPageUrl($page),
            'is_current' => $is_current
        ];
    }

    private function createPageEllipsis() {
        return [
            'num'        => '...',
            'url'        => null,
            'is_current' => false
        ];
    }

    public function getRendered() {

        if ($this->pages_count <= 1) {
            return '';
        }

        $from = $this->current_page * $this->perpage - $this->perpage + 1;
        $to   = $this->current_page * $this->perpage; if ($to > $this->total) { $to = $this->total; }

        $template = cmsTemplate::getInstance();

        return $template->getRenderedAsset('ui/'.$template->getOption('pagination_template', 'pagination'), [
            'stat_hint' => sprintf(LANG_PAGES_SHOWN, $from, $to, $this->total),
            'current_page' => $this->current_page,
            'prev_url' => $this->getPrevUrl(),
            'next_url' => $this->getNextUrl(),
            'pages'    => $this->getPages()
        ]);
    }

}
