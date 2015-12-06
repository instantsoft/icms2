<?php

class fieldListBitmask extends cmsFormField {

    public $title = LANG_PARSER_LIST_MULTIPLE;
    public $sql   = 'varchar(64) NULL DEFAULT NULL';
	public $allow_index = true;
	public $filter_type = 'str';

    public function getFilterInput($value) {

        $items = $this->getListItems();

		$value = is_array($value) ? $value : array();
		return html_select_multiple($this->name, $items, $value);

    }

    public function parse($value){

		if (!$value) { return LANG_NO; }

        $items = $this->getListItems();

		$html = '';

		if ($items) {
			$pos = 0;
			$html .= '<ul>';
			foreach($items as $key => $item){
				if (mb_substr($value, $pos, 1) == 1){
					$html .= '<li>' . htmlspecialchars($item) . '</li>';
				}
				$pos++;
				if ($pos+1 > mb_strlen($value)) { break; }
			}
			$html .= '</ul>';
		}

        return $html;

    }

    public function getListItems(){

        $items = array();

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = $this->parseListItems($this->getDefaultValue());

        }

        return $items;

    }

    public function parseListItems($string){
        return string_explode_list($string);
    }

	public function parseValue($values){

		if (!$values) { return false; }

		$items = $this->getListItems();
		$value = '';

		if ($items){
			foreach($items as $key => $title){
				$value .= in_array($key, $values) ? '1' : '0';
			}
		}

		return $value;

	}

	public function store($value, $is_submitted, $old_value=null){

        $value = $this->parseValue($value);

		if (mb_strpos($value, "1") === false){
			return "";
		}

        return $value;

    }

    public function applyFilter($model, $values) {

		if (!is_array($values)) { return $model; }

		$filter = $this->parseValue($values);
		$filter = str_replace("0", "_", $filter) . '%';

		$model->filterLike($this->name, $filter);

        return $model;

    }

    public function getInput($value){

        $this->data['items']    = $this->getListItems();
        $this->data['selected'] = array();

        if($value){
            if(!is_array($value)){
                $pos = 0;
                foreach($this->data['items'] as $key => $title){
                    if(mb_substr($value, $pos, 1) == 1){
                        $this->data['selected'][] = $key;
                    }
                    $pos++;
                    if($pos+1 > mb_strlen($value)){break;}
                }
            }else{
                $this->data['selected'] = $value;
            }
        }

        return parent::getInput($value);

    }

}
