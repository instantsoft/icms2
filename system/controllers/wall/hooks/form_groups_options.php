<?php

class onWallFormGroupsOptions extends cmsAction {

    public function run($data) {

        list($form, $params) = $data;

        $form->addField('view', new fieldCheckbox('is_wall', [
            'title' => LANG_GROUPS_OPT_WALL_ENABLED
        ]));

        return [$form, $params];
    }

}
