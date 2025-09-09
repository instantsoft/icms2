<?php

class formPayeerSystemOptions extends cmsForm {

    public function init() {

        return [
            'options' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('options:shop_id', [
                        'title' => LANG_BILLING_SYSTEM_PAYEER_SHOP_ID,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:secret_key', [
                        'title'       => LANG_BILLING_SYSTEM_PAYEER_SECRET_KEY,
                        'is_password' => true,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:sig_key', [
                        'title'       => LANG_BILLING_SYSTEM_PAYEER_SIG_KEY,
                        'is_password' => true,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:curr', [
                        'title' => LANG_BILLING_SYSTEM_PAYEER_CURR,
                        'hint'  => 'USD, RUB, EUR, BTC, ETH, BCH, LTC, DASH, USDT, XRP, DOGE, TRX, BNB, POL, DAI, DOT, USDC, LINK, SAND, MANA, AAVE, SUSHI, CAKE, 1INCH, GALA, LDO, GMT, UNI, CRV, BAL, GRT, APE, TON, INJ, ATOM, OKB, IMX, CRO, QNT, FDUSD, RENDER, ARB, AXS, SNX, MKR, BEAM, DYDX, FET, CHZ, CHEEL, CFX, ILV, FXS, FRAX, BLUR, RPL, TWT, NEXO, GNO, ELF, IOTX, ZIL, PAXG, TRB, HOT, ENJ, COMP, BAT, WLD, USDP, LRC, SFP, JASMY, BICO, ANKR, ZRX, SUPER, PRIME, ENS, MASK, CVX, AUDIO, YFI, MEME, SKL, LPT, PYUSD, AUCTION, BAND, GLM, METIS, TRAC, DAO, AMP',
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
