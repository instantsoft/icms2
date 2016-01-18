<?php

class onTypographHidetext extends cmsAction {

    public function run($data){
		
		if (!is_array($data)){ return $this->parseHide($data); }
		
		if ($data) {
			foreach($data as $i => $item){
				$data[$i]['content_html'] = $this->parseHide($item['content_html']);
			}
		}

        return $data;
    }
	
    private function parseHide($text){

        $pattern = '/\[hide(?:=?)([0-9]*)\](.*?)\[\/hide\]/sui';
		
		preg_match($pattern, $text, $matches);

		if (!cmsUser::isLogged()){
			$replacement = '<noindex><div class="hidetext">'.LANG_HIDE_TEXT.'</div></noindex>';
		} else {
			if(!$matches[1]){
				$replacement = '<div class="hidetext">${2}</div>';
			} elseif(cmsUser::get('karma') >= $matches[1] || cmsUser::isAdmin()) {
				$replacement = '<div class="hidetext">${2}</div>';
			} else {
				$replacement = '<div class="hidetext">'.sprintf(LANG_HIDE_TEXT_KARMA, spellCount($matches[1], LANG_HIDE_TEXT_ONE, LANG_HIDE_TEXT_TWO, LANG_HIDE_TEXT_MANY)).'</div>';
			}

		}
		return preg_replace($pattern, $replacement, $text);
    }
}