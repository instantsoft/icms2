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

        if ($this->options['is_refs']) {

            $ref_id = (int) cmsUser::getCookie('ref_id');

            if (!empty($user['inviter_id'])) {
                $ref_id = $user['inviter_id'];
            }

            if ($ref_id) {

                cmsUser::unsetCookie('ref_id');

                $ref_user = $this->model_users->getUser($ref_id);

                if ($ref_user) {

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
                }
            }
        }

        return $user;
    }

}
