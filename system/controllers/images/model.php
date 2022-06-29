<?php

class modelImages extends cmsModel{

	public function getPresets(){
		return $this->get('images_presets', function($item, $model){
			$item['wm_image'] = cmsModel::yamlToArray($item['wm_image']);
			return $item;
		});
	}

	public function getPresetsList($with_params = false){
		return $this->filterIsNull('is_internal')->
				orderBy('width')->
				get('images_presets', function($item, $model) use($with_params){
                    if($with_params){
                        return $item['title'].' ('.$item['name'].', '.($item['width'] ? $item['width'] : LANG_AUTO).' x '.($item['height'] ? $item['height'] : LANG_AUTO).')';
                    }
					return $item['title'];
				}, 'name');
	}

	public function getPreset($id){
		return $this->getItemById('images_presets', $id, function($item, $model){
			$item['wm_image'] = cmsModel::yamlToArray($item['wm_image']);
			return $item;
		});
	}

	public function getPresetByName($name){
		return $this->getItemByField('images_presets', 'name', $name, function($item, $model){
			$item['wm_image'] = cmsModel::yamlToArray($item['wm_image']);
			return $item;
		});
	}

	public function addPreset ($preset) {
		return $this->insert('images_presets', $preset);
	}

	public function updatePreset ($id, $preset) {
		return $this->update('images_presets', $id, $preset);
	}

	public function deletePreset ($id) {
		return $this->delete('images_presets', $id);
	}

}
