<?php
/**
 * @property \modelBilling $model
 */
class onBillingContentBeforeItem extends cmsAction {

    public function run($data) {

        list($ctype, $item, $fields) = $data;

        if ($this->cms_user->is_admin || $this->cms_user->id == $item['user_id']) {
            return $data;
        }

        $paid_fields = $this->model->getContentTypePaidFields($ctype['id']);
        if (!$paid_fields) {
            return $data;
        }

        $btn_temlate = !empty($this->options['pay_field_html']) ? $this->options['pay_field_html'] : '';

        $is_found = false;

        foreach ($paid_fields as $name => $paid_field) {

            if (!isset($fields[$name])) {
                continue;
            }

            if (is_empty_value($fields[$name]['html'] ?? '')) {
                continue;
            }

            $price = $this->getPaidFieldPrice($paid_field, $item);

            if ($this->cms_user->is_logged) {

                if (!$price) {
                    continue;
                }

                if ($this->model->isPaidFieldPurchased($this->cms_user->id, $paid_field['id'], $item['id'])) {
                    continue;
                }

                $btn_title = !empty($paid_field['btn_titles']['user']) ?
                                    $paid_field['btn_titles']['user'] :
                                    ($this->options['btn_titles']['user'] ?? '');

            } else {
                // Нет цены для гостей, ставим минимальную
                if (!$price) {

                    $prices = [];
                    foreach ($paid_field['prices'] as $p) {
                        if($p != 0){
                            $prices[] = $p;
                        }
                    }
                    $price = $prices ? min($prices) : 0;
                }

                $btn_title = !empty($paid_field['btn_titles']['guest']) ?
                                    $paid_field['btn_titles']['guest'] :
                                    ($this->options['btn_titles']['guest'] ?? '');
            }

            $buy_url = href_to($this->name, 'buy', [$paid_field['id'], $item['id']]);
            if (!$this->cms_user->is_logged) {
                $buy_url = href_to('auth', 'login', [], ['back' => $buy_url]);
            }

            if ($btn_temlate) {

                $patterns = [
                    'url'   => $buy_url,
                    'price' => $price ? html_spellcount($price, $this->options['currency']) : null
                ];

                $btn_title = string_replace_keys_values_extended($btn_title, $patterns);

                $patterns['title'] = $btn_title;

                $buy_html = string_replace_keys_values_extended(string_replace_svg_icons($btn_temlate), $patterns);

            } else {

                $buy_text = $price ? sprintf(LANG_BILLING_BUY_FOR, html_spellcount($price, $this->options['currency'])) : LANG_BILLING_BUY;

                $buy_html = '<div class="billing-buy-field"><a href="' . $buy_url . '">' . $buy_text . '</a></div>';
            }

            $fields[$name]['html'] = $buy_html;

            $is_found = true;
        }

        if ($is_found) {
            $this->cms_template->addCSS($this->cms_template->getStylesFileName('billing'));
        }

        return [$ctype, $item, $fields];
    }

}
