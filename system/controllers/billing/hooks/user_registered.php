<?php
/**
 * @property \modelBilling $model
 * @property \modelUsers $model_users
 */
class onBillingUserRegistered extends cmsAction {

    public function run($user) {

        if ($this->options['reg_bonus']) {
            $this->model->incrementUserBalance($user['id'], $this->options['reg_bonus'], LANG_BILLING_REG_BONUS_LOG);
        }

        if (!$this->options['is_refs']) {
            return $user;
        }

        $ref_id = (int) cmsUser::getCookie('ref_id');

        if (empty($this->options['keep_cookie_after_reg'])) {
            cmsUser::unsetCookie('ref_id');
        }

        // Инвайты в приоритете
        if (!empty($user['inviter_id'])) {
            $ref_id = $user['inviter_id'];
        }

        // До хука проверяем права доступа
        if ($ref_id) {

            $ref_user = $this->model_users->getUser($ref_id);

            // Проверяем, мог ли юзер делиться рефссылками
            if ($ref_user) {

                if (!cmsUser::isUserInGroups($ref_user['groups'], $this->options['refs_groups'])) {
                    return $user;
                }

            } else {
                $ref_id = 0;
            }
        }

        [$ref_id, $user] = cmsEventsManager::hook('billing_user_registered_referal', [$ref_id, $user]);

        if (!$ref_id) {
            return $user;
        }

        $ref_user = $this->model_users->getUser($ref_id);

        if (!$ref_user) {
            return $user;
        }

        if (!empty($this->options['is_refs_as_invite'])) {

            $this->model_users->updateUser($user['id'], [
                'inviter_id' => $ref_user['id']
            ]);
        }

        if ($this->options['ref_type'] === 'collect') {

            $root_id = $this->model->getReferalRootAncestorId($ref_id);

            $ref_id  = $this->model->getNextReferalAncestor($root_id, $this->options['ref_scale']);
        }

        $link_id = $this->model->addReferal($user['id'], $ref_id);

        if ($this->options['ref_bonus'] && $link_id) {
            $this->payRefRegBonus($user['id'], $ref_id, $link_id);
        }

        return $user;
    }

}
