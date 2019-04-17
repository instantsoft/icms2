<?php

class modelWysiwygs extends cmsModel {

	public function getPresets(){
		return $this->limit(false)->get('wysiwygs_presets', function($item, $model){
			$item['options'] = cmsModel::stringToArray($item['options']);
			return $item;
		});
	}

	public function getPreset($id){
		return $this->getItemById('wysiwygs_presets', $id, function($item, $model){
			$item['options'] = cmsModel::stringToArray($item['options']);
			return $item;
		});
	}

	public function addPreset ($preset) {
		return $this->insert('wysiwygs_presets', $preset, true);
	}

	public function updatePreset ($id, $preset) {
		return $this->update('wysiwygs_presets', $id, $preset, false, true);
	}

	public function getPresetsList(){
		return (array)$this->orderBy('wysiwyg_name')->get('wysiwygs_presets', function($item, $model){
            return ucfirst($item['wysiwyg_name']).': '.$item['title'];
        }, 'id');
	}

	public function deletePreset ($id) {
		return $this->delete('wysiwygs_presets', $id);
	}

}
