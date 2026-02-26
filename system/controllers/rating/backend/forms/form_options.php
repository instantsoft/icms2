<?php

class formRatingOptions extends cmsForm {

    public function init() {

        return [
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_hidden', [
                        'title' => LANG_RATING_IS_HIDDEN
                    ]),
                    new fieldCheckbox('is_show', [
                        'title' => LANG_RATING_SHOW_INFO
                    ]),
                    new fieldCheckbox('allow_guest_vote', [
                        'title' => LANG_RATING_ALLOW_GUEST_VOTE
                    ]),
                    new fieldCheckbox('allow_changing_votes', [
                        'title' => LANG_RATING_ALLOW_CHANGE_VOTE,
                        'hint'  => LANG_RATING_ALLOW_CHANGE_VOTE_HINT
                    ]),
                    new fieldCheckbox('allow_changing_votes_session', [
                        'title' => LANG_RATING_ALLOW_CHANGE_VOTE_SESSION,
                        'hint'  => LANG_RATING_ALLOW_CHANGE_VOTE_SESSION_HINT,
                        'visible_depend' => ['allow_changing_votes' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('disable_negative_votes', [
                        'title' => LANG_RATING_DISABLE_NEGATIVE_VOTES,
                        'hint'  => LANG_RATING_ALLOW_CHANGE_VOTE_HINT
                    ]),
                    new fieldList('template', [
                        'title' => LANG_RATING_TEMPLATE,
                        'hint'  => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/rating/widget*'),
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/rating', 'widget*.tpl.php');
                        }
                    ])

                ]
            ]
        ];
    }

}
