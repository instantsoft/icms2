<?php

class forms extends cmsFrontend {

    protected $useOptions = true;

    protected $unknown_action_as_index_param = true;

    public function getOptions(){

        $options = (array)self::loadOptions($this->name);

        if(!empty($options['allow_embed_domain'])){
            $allow_embed_domain_array = explode(',', $options['allow_embed_domain']);
            $options['allow_embed_domain_array'] = array_map(function($val){ return trim($val); }, $allow_embed_domain_array);
        }
        if(!empty($options['denied_embed_domain'])){
            $allow_embed_domain_array = explode(',', $options['denied_embed_domain']);
            $options['denied_embed_domain_array'] = array_map(function($val){ return trim($val); }, $allow_embed_domain_array);
        }

        return $options;
    }

    public function isAllowEmbed() {

        if(empty($this->options['allow_embed'])){
            return false;
        }

        $is_external    = true;
        $show_on_domain = true;

        if(isset($_SERVER['HTTP_REFERER'])){

            $refer      = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            $is_external = (($refer == $_SERVER['HTTP_HOST']) ? false : true);

            if(!empty($this->options['allow_embed_domain_array']) && $is_external){
                $show_on_domain = ($refer && in_array($refer, $this->options['allow_embed_domain_array']));
            }

            if(!empty($this->options['denied_embed_domain_array']) && $is_external){
                $show_on_domain = !($refer && in_array($refer, $this->options['denied_embed_domain_array']));
            }

        }

        return $show_on_domain;
    }

    public function parseShortcode($string) {

        $matches_count = preg_match_all('/{forms:([a-z0-9_]+)}/i', $string, $matches);

        if ($matches_count) {
            for ($i = 0; $i < $matches_count; $i++) {

                $tag  = $matches[0][$i];
                $name = $matches[1][$i];

                $_form_data = $this->model->getFormData($name);

                if ($_form_data === false) {
                    continue;
                }

                list($form, $form_data) = $_form_data;

                $form_html = $this->cms_template->renderInternal($this, 'form_view', [
                    'form_data' => $form_data,
                    'form'      => $form
                ]);

                $string = str_replace($tag, $form_html, $string);
            }
        }

        return $string;
    }

}
