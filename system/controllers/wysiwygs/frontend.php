<?php

class wysiwygs extends cmsFrontend {

    public function getEditorParams($options = []) {

        if(empty($options['options'])){
            $options['options'] = [];
        }

        $result = array_merge([
            'editor'  => $this->cms_config->default_editor,
            'options' => []
        ], $options);

        if (is_numeric($result['editor'])) {

            $preset = $this->model->getPreset($result['editor']);

            if (!$preset) {
                return [
                    'editor'  => $this->cms_config->default_editor,
                    'options' => []
                ];
            }

            $result['editor']  = $preset['wysiwyg_name'];
            $result['options'] = array_merge($preset['options'], $options['options']);
        }

        if (!empty($options['presets'])) {

            $preset_id = 0;

            foreach ($this->cms_user->groups as $group_id) {
                foreach ($options['presets'] as $editor_preset) {
                    if ($group_id == $editor_preset['group_id']) {
                        if (is_numeric($editor_preset['preset_id'])) {
                            $preset_id = $editor_preset['preset_id'];
                        } else {
                            $preset_id         = 0;
                            $result['editor']  = $editor_preset['preset_id'];
                            $result['options'] = [];
                        }
                    }
                }
            }

            if ($preset_id) {

                $preset = $this->model->getPreset($preset_id);

                if ($preset) {
                    $result['editor']  = $preset['wysiwyg_name'];
                    $result['options'] = array_merge($preset['options'], $options['options']);
                }
            }
        }

        if (!$result['editor']) {
            $result['editor'] = null;
        }

        return $result;
    }

}
