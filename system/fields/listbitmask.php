<?php

class fieldListBitmask extends cmsFormField {

    public $title       = LANG_PARSER_LIST_MULTIPLE;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $allow_index = true;
    public $filter_type = 'str';
    public $var_type    = 'array';

    public function getOptions(){
        return array(
            new fieldNumber('max_length', array(
                'title'   => LANG_PARSER_BITMASK_MAX,
                'hint'    => LANG_PARSER_BITMASK_MAX_HINT,
                'default' => 64,
                'rules' => array(
                    array('min', 1)
                )
            ))
        );
    }

    public function getFilterInput($value) {

        $items = $this->getListItems();

        if(is_array($value)){
            foreach ($value as $k => $v) {
                if(is_numeric($v)){ $value[$k] = intval($v); }
            }
        } else {
            $value = array();
        }

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

            $items = string_explode_list($this->getDefaultValue());

        }

        return $items;

    }

    public function setOptions($options){
        parent::setOptions($options);
        if (!isset($this->items) && $this->hasDefaultValue()){
            $this->items = string_explode_list($this->getDefaultValue());
            $this->default = null;
        }
    }

	public function parseValue($values){

		if (!$values) { return ''; }

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

		if (mb_strpos($value, '1') === false){
			return '';
		}

        return $value;

    }

    public function applyFilter($model, $values) {

		if (!is_array($values)) { return parent::applyFilter($model, $values); }

		$filter = $this->parseValue($values);
        if (!$filter) { return parent::applyFilter($model, $values); }

		$filter = str_replace('0', '_', $filter) . '%';

		return $model->filterLike($this->name, $filter);

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