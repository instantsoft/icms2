<?php

class onContentUserNotifyTypes extends cmsAction {

    public function run() {

        $types = [];

        $ctypes = $this->model->getContentTypes();

        foreach ($ctypes as $ctype) {

			if (!$ctype['is_date_range']) { continue; }
			if (empty($ctype['options']['notify_end_date_days'])) { continue; }

            // проверяем наличие доступа
            if (!cmsUser::isAllowed($ctype['name'], 'add') || !isset($ctype['labels']['many'])) { continue; }
            if (!cmsUser::isAllowed($ctype['name'], 'pub_long')) { continue; }

            $types['notify_expired_'.$ctype['name']] = array(
                'title'   => sprintf(LANG_CONTENT_NOTIFY_END_DATE, $ctype['labels']['many'])
            );

        }

        return $types ? $types : false;

    }

}
