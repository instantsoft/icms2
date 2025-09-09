<?php
/**
 * @property \modelMessages $model
 */
class onMessagesMenuMessages extends cmsAction {

    public function run($item) {

        if (!$this->cms_user->is_logged) {
            return false;
        }

        switch ($item['action']) {
            case 'view':

                if (!empty($this->options['is_enable_pm'])) {

                    $count = $this->model->getNewMessagesCount($this->cms_user->id);

                    return [
                        'url'     => href_to($this->name),
                        'counter' => $count
                    ];
                }

                break;
            case 'notices':

                $count = $this->model->getNoticesCount($this->cms_user->id);

                if (!empty($this->options['hide_zero_notices_menu']) && !$count) {
                    return false;
                }

                return [
                    'url'     => href_to($this->name, 'notices'),
                    'counter' => $count
                ];

            default:
                break;
        }

        return false;
    }

}
