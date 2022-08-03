<?php

class actionAdminControllersEventsUpdate extends cmsAction {

    public function run() {

        $diff_events = $this->getEventsDifferences();

        if ($diff_events['added']) {
            foreach ($diff_events['added'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->model->addEvent($controller, $event);
                }
            }
        }

        if ($diff_events['deleted']) {
            foreach ($diff_events['deleted'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->model->deleteEvent($controller, $event);
                }
            }
        }

        cmsUser::addSessionMessage(LANG_EVENTS_SUCCESS, 'success');

        return $this->redirectToAction('controllers', ['events']);
    }

}
