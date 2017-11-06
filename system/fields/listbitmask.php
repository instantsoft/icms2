<?php

class fieldListBitmask extends cmsFormField {

    public $title       = LANG_PARSER_LIST_MULTIPLE;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $allow_index = true;
    public $filter_type = 'str';
    public $var_type    = 'array';

    public function getOptions(){
        return array(
            new fieldCheckbox('is_checkbox_multiple', array(
                'title'   => LANG_PARSER_BITMASK_CHECKBOX_MULTIPLE,
                'default' => true
            )),
            new fieldString('list_class', array(
                'title'   => LANG_PARSER_BITMASK_LIST_CLASS,
                'default' => 'multiple_tags_list'
            )),
            new fieldNumber('max_length', array(
                'title'   => LANG_PARSER_BITMASK_MAX,
                'hint'    => LANG_PARSER_BITMASK_MAX_HINT,
                'default' => 64,
                'rules' => array(
                    array('min', 1)
                )
            )),
            new fieldCheckbox('is_autolink', array(
                'title' => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'  => LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default' => false
            ))
        );
    }

    public function getFilterInput($value) {

        $this->data['items']    = $this->getListItems();
        $this->data['selected'] = array();

        if(is_array($value)){
            foreach ($value as $k => $v) {
                if(is_numeric($v)){ $this->data['selected'][$k] = intval($v); }
            }
        } else {
            $this->data['selected'] = array();
        }

        $this->element_title = '';

        return parent::getInput($value);

    }

    public function getStringValue($value){

        if (!$value) { return LANG_NO; }

        $items = $this->getListItems();

        $string = '';

		if ($items) {
            $string = implode(', ', $items);
		}

        return $string;

    }

    public function parse($value){

		if (!$value) { return LANG_NO; }

        $items = $this->getListItems();

		$html = '';

		if ($items) {
            $is_autolink = $this->getOption('is_autolink');
			$pos = 0;
			$html .= '<ul class="'.$this->getOption('list_class').'">';
			foreach($items as $key => $item){
				if (substr($value, $pos, 1) == 1){
                    if($is_autolink){
                        $html .= '<li><a class="listbitmask_autolink '.$this->item['ctype_name'].'_listbitmask_autolink" href="'.href_to($this->item['ctype_name']).'?'.$this->name.'='.urlencode($pos+1).'">'.htmlspecialchars($item).'</a></li>';
                    } else {
                        $html .= '<li>' . htmlspecialchars($item) . '</li>';
                    }
				}
				$pos++;
				if ($pos+1 > strlen($value)) { break; }
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
