<?php

class modelImages extends cmsModel {

    use icms\traits\controllers\models\transactable;

    /**
     * Проверяет лимит загрузки изображений по алгоритму Token Bucket
     *
     * @param int $user_id ID пользователя
     * @param int $capacity Максимальное количество токенов
     * @param int $refill_seconds Время восстановления одного токена в секундах
     *
     * @return int 0 — загрузка разрешена, иначе количество секунд ожидания
     */
    public function filesCheckUploadRateLimit($user_id, $capacity, $refill_seconds = 30) {

        if (!$user_id || $capacity < 0 || $refill_seconds <= 0) {
            return PHP_INT_MAX;
        }

        if (!$capacity) {
            return 0;
        }

        $this->startTransaction();

        // Создаём корзину пользователя, если её ещё нет
        $this->insert('upload_rate_limits', [
            'user_id' => $user_id,
            'tokens' => $capacity
        ], false, true);

        $bucket = $this->forUpdate()->
                select('UNIX_TIMESTAMP(i.date_updated)', 'updated_time')->
                select('UNIX_TIMESTAMP()', 'current_time')->
                getItemByField('upload_rate_limits', 'user_id', $user_id);

        if (!$bucket) {
            $this->endTransaction(false);
            return PHP_INT_MAX;
        }

        $now = (int) $bucket['current_time'];

        // Восстанавливаем токены с момента последнего обновления
        $elapsed = max(0, $now - (int) $bucket['updated_time']);

        $tokens = min(
            (float) $capacity,
            (float) $bucket['tokens'] + $elapsed / $refill_seconds
        );

        $wait = 0;

        if ($tokens >= 1) {
            $tokens -= 1;
        } else {
            $wait = (int) ceil((1 - $tokens) * $refill_seconds);
        }

        $success = $this->filterEqual('user_id', $user_id)->
                updateFiltered('upload_rate_limits', [
                    'date_updated' => null, // CURRENT_TIMESTAMP
                    'tokens' => $tokens
                ]);

        $this->endTransaction($success);

        return $wait;
    }

    protected function presetCallback(array $item, cmsModel $model) {

        $item['wm_image'] = cmsModel::yamlToArray($item['wm_image']);

        foreach (['width', 'height', 'wm_margin', 'quality', 'crop_position'] as $int_name) {
            $item[$int_name] = (int) $item[$int_name];
        }

        return $item;
    }

    public function getPresets() {
        return $this->get('images_presets', [$this, 'presetCallback']);
    }

    public function getPresetsList($with_params = false) {
        return $this->filterIsNull('is_internal')->
            orderBy('width')->
            get('images_presets', function ($item, $model) use ($with_params) {
                if ($with_params) {
                    return $item['title'] . ' (' . $item['name'] . ', ' . ($item['width'] ? $item['width'] : LANG_AUTO) . ' x ' . ($item['height'] ? $item['height'] : LANG_AUTO) . ')';
                }
                return $item['title'];
            }, 'name') ?: [];
    }

    public function getPreset($id) {
        return $this->getItemById('images_presets', $id, [$this, 'presetCallback']);
    }

    public function getPresetByName(string $name) {
        return $this->getItemByField('images_presets', 'name', $name, [$this, 'presetCallback']);
    }

    public function addPreset(array $preset) {
        return $this->insert('images_presets', $preset);
    }

    public function updatePreset($id, $preset) {
        return $this->update('images_presets', $id, $preset);
    }

    public function deletePreset($id) {
        return $this->delete('images_presets', $id);
    }

}
