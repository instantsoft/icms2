<?php

class modelWysiwygs extends cmsModel {

    protected function itemCallback($item, $model) {

        $item['options'] = self::stringToArray($item['options']);

        return $item;
    }

    public function getPresets() {
        return $this->limit(false)->get('wysiwygs_presets', [$this, 'itemCallback']);
    }

    public function getPreset($id) {
        return $this->getItemById('wysiwygs_presets', $id, [$this, 'itemCallback']);
    }

    public function addPreset($preset) {
        return $this->insert('wysiwygs_presets', $preset, true);
    }

    public function updatePreset($id, $preset) {
        return $this->update('wysiwygs_presets', $id, $preset, false, true);
    }

    public function getPresetsList() {
        return $this->orderBy('wysiwyg_name')->get('wysiwygs_presets', function ($item, $model) {
            return ucfirst($item['wysiwyg_name']) . ': ' . $item['title'];
        }, 'id') ?: [];
    }

}
