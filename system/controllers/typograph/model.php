<?php

class modelTypograph extends cmsModel {

    protected function itemCallback($item, $model) {

        $item['options'] = cmsModel::yamlToArray($item['options']);

        return $item;
    }

    public function getPresets() {
        return $this->limit(false)->get('typograph_presets', [$this, 'itemCallback']);
    }

    public function getPreset($id) {
        return $this->getItemById('typograph_presets', $id, [$this, 'itemCallback']);
    }

    public function addPreset($preset) {
        return $this->insert('typograph_presets', $preset);
    }

    public function updatePreset($id, $preset) {
        return $this->update('typograph_presets', $id, $preset);
    }

}
