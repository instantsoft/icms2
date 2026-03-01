<?php
/**
 * @property \modelBilling $model
 */
class actionBillingDeposit extends cmsAction {

    // Подключаем валидацию перед выполнением метода run
    use \icms\controllers\billing\traits\validatepay;

    public function run() {

        if ($this->options['in_mode'] !== 'enabled' && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $is_new_top_up = $this->request->has('new_top_up');

        $amount = abs($this->request->get('amount', 0.0));

        $systems = $this->model->getPaymentSystems($this->cms_user->is_admin ? 2 : 1);

        $ticket = cmsUser::sessionGet('billing_ticket') ?: [];

        if ($is_new_top_up) {

            $ticket = [];

            cmsUser::sessionUnset('billing_ticket');
        }

        $min_pack = $ticket['diff_amount'] ?? $this->options['min_pack'];

        $b_spellcount = $this->options['currency'];
        $b_spellcount_arr = explode('|', $b_spellcount);

        $min_amount = $amount ? $amount : ($min_pack ? $min_pack : 10);

        $balance = $this->model->getUserBalance($this->cms_user->id);

        $show_price_block = true;

        if (count($this->options['prices']) === 1) {
            $price = reset($this->options['prices']);
            if ($price['amount'] == $price['price']) {
                $show_price_block = false;
            }
        }

        return $this->cms_template->render([
            'user'             => $this->cms_user,
            'balance'          => $balance,
            'amount'           => $amount,
            'systems_list'     => array_column($systems, 'title', 'name'),
            'min_pack'         => $min_pack,
            'ticket'           => $ticket,
            'min_amount'       => $min_amount,
            'b_spellcount'     => $b_spellcount,
            'b_spellcount_arr' => $b_spellcount_arr,
            'curr'             => $this->options['currency_real'],
            'curr_symb'        => $this->options['cur_real_symb'],
            'prices'           => $this->options['prices'],
            'show_price_block' => $show_price_block
        ]);
    }

}
