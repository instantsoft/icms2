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

        if(!$this->show_filter_input_title){
            $this->element_title = '';
        }

        return $this->getInput($value);

    }

    public function getStringValue($value){

        if (!$value) { return ''; }

        $items = $this->getListItems();

        $string = '';

		if ($items) {

			$pos = 0; $list = array();

			foreach($items as $key => $item){

                if(!is_array($value)){

                    if (substr($value, $pos, 1) == 1){
                        $list[] = $item;
                    }
                    $pos++;
                    if ($pos+1 > strlen($value)) { break; }

                } else {

                    if(in_array($key, $value)){
                        $list[] = $item;
                    }

                }

			}

            $string = implode(', ', $list);

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

            ksort($items);

        }

        return $items;

    }

    public function setOptions($options){
        parent::setOptions($options);
        if (!isset($this->items) && $this->hasDefaultValue()){
            $this->items = string_explode_list($this->getDefaultValue());
            ksort($this->items);
            $this->default = null;
        }
    }

	public function parseValue($values, $return_as_array = false){

		if (!$values || !is_array($values)) { return ''; }

		$items = $this->getListItems();
		$value = $return_as_array ? [] : '';

		if ($items){
			foreach($items as $key => $title){
                if($return_as_array){
                    if(in_array($key, $values)){
                        $value[] = $key;
                    }
                } else {
                    $value .= in_array($key, $values) ? '1' : '0';
                }
			}
		}

		return $value;

	}

	public function store($value, $is_submitted, $old_value=null){

        $value = $this->parseValue($value, ($this->context === 'filter'));

		if (is_string($value) && mb_strpos($value, '1') === false){
			return '';
		}

        return $value;

    }

    public function storeFilter($value){
        return $this->store($value, false);
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
        $this->data['selected'] = [];

        if($value){
            if(!is_array($value)){
                $pos = 0;
                foreach($this->data['items'] as $key => $title){
                    if(mb_substr($value, $pos, 1) == 1){
                        $this->data['selected'][] = is_numeric($key) ? intval($key) : $key;
                    }
                    $pos++;
                    if($pos+1 > mb_strlen($value)){ break; }
                }
            } else {

                foreach ($value as $k => $v) {
                    $this->data['selected'][] = is_numeric($v) ? intval($v) : $v;
                }

            }
        }

        return parent::getInput($value);

    }

    public function hookAfterUpdate($content_table_name, $field, $field_old, $model){

        $items = $model->limit(false)->
                selectOnly('id')->
                select($field_old['name'])->
                get($content_table_name, function($item, $model)use($field_old){
                    return $item[$field_old['name']];
                });

        if(!$items || trim($field_old['values']) == trim($field['values'])){
            return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);
        }

        $old_rows = string_explode_list($field_old['values']);
        ksort($old_rows);
        $new_rows = string_explode_list($field['values']);
        ksort($new_rows);

        foreach ($items as $id => $item_value) {

            if(!$item_value){ continue; }

            // Формируем старый массив значений
            $old_item_values = [];

			$pos = 0;

			foreach($old_rows as $key => $value){
				if (substr($item_value, $pos, 1) == 1){
                    $old_item_values[] = $key;
				}
				$pos++;
			}

            // Формируем новую битовую маску
            $new_item_value = '';

			foreach($new_rows as $nkey => $title){
                $new_item_value .= in_array($nkey, $old_item_values) ? '1' : '0';
			}

            // записываем обратно в базу
            $model->update($content_table_name, $id, [
                $field_old['name'] => $new_item_value
            ], true);

        }

        return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);

    }

}
