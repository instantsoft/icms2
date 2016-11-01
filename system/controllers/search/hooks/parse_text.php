<?php

class onSearchParseText extends cmsAction {

    public function run($text){

        if(empty($this->options['is_hash_tag'])){ return $text; }

        return $this->parseHashTag($text);

    }

}
