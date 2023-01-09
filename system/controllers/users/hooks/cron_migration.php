<?php

class onUsersCronMigration extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $rules = $this->model->filterEqual('is_active', 1)->get('{users}_groups_migration');

        if (!$rules) {
            return true;
        }

        foreach ($rules as $rule) {

            // Получаем юзеров, которые уже в нужной группе
            // Чтобы несколько раз не переводить
            $to_user_ids = $this->model->limit(false)->
                    filterEqual('group_id', $rule['group_to_id'])->
                    get('{users}_groups_members', function($user, $model) {
                return $user['user_id'];
            }, false);

            // Если есть, исключаем
            if($to_user_ids){
                $this->model->filterNotIn('id', $to_user_ids);
            }

            // Фильтруем по группе, с которой переводим
            $this->model->filterGroup($rule['group_from_id']);

            $this->model->filterIsNull('is_locked');
            $this->model->filterIsNull('is_deleted');

            $this->model->selectOnly('i.id');
            $this->model->select('i.date_group')->select('i.date_reg');
            $this->model->select('i.rating')->select('i.karma');
            $this->model->select('i.groups');

            // Ограничения по рейтингу
            if ($rule['is_rating']) {
                $this->model->filterGtEqual('rating', $rule['rating']);
            }
            // Ограничения по карме
            if ($rule['is_karma']) {
                $this->model->filterGtEqual('karma', $rule['karma']);
            }
            // Ограничения по дате
            if ($rule['is_passed']) {

                $passed_field = $rule['passed_from'] ? 'date_group' : 'date_reg';

                $this->model->filterDateOlder($passed_field, $rule['passed_days']);
            }

            $users = $this->model->limit(false)->get('{users}', function($user, $model) {
                $user['groups'] = cmsModel::yamlToArray($user['groups']);
                return $user;
            }, false);
            if (!$users) { continue; }

            foreach ($users as $user) {

                // Меняем группу
                if (!$rule['is_keep_group']) {
                    if (($key = array_search($rule['group_from_id'], $user['groups'])) !== false) {
                        unset($user['groups'][$key]);
                    }
                }

                $user['groups'][] = $rule['group_to_id'];
                $user['groups'] = array_unique($user['groups']);

                $this->model->updateUser($user['id'], [
                    'groups'     => $user['groups'],
                    'date_group' => null
                ]);

                // Уведомление
                if (!$rule['is_notify']) {
                    continue;
                }

                $this->controller_messages->addRecipient($user['id']);

                $this->controller_messages->sendNoticePM(['content' => nl2br($rule['notify_text'])]);

                $this->controller_messages->clearRecipients();
            }
        }

        return true;
    }

}
