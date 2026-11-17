<?php

namespace icms\controllers\content\traits;

use cmsUser;

/**
 * Трейт проверок доступа к контенту
 */
trait contentAccessTrait {

    protected $viewed_moderators = false;

    /**
     * Проверяет все ограничения доступа к контенту
     *
     * @param array $ctype       Данные типа контента
     * @param array $item        Массив записи
     * @param bool $is_moderator Флаг, что пользователь модератор
     * @return array [true|'forbidden'|'not_found'|'redirect', ?string, ?string]
     */
    public function checkContentItemAccess(array $ctype, array $item, bool $is_moderator) {

        $notices = [];

        // Модерация и черновики
        [$is_allow_view, $notice] = $this->checkModerationAccess($ctype, $item, $is_moderator);

        if (!$is_allow_view) {
            return ['forbidden', $notice, null];
        }

        if ($notice) {
            $notices[] = $notice;
        }

        // общие права доступа на просмотр
        [$is_allow_view, $notice] = $this->checkItemAccess($ctype, $item, $is_moderator);

        if ($is_allow_view !== true) {
            return [
                $is_allow_view === false ? 'forbidden' : 'not_found',
                null,
                null
            ];
        }

        if ($notice) {
            $notices[] = $notice;
        }

        // Проверяем ограничения доступа из других контроллеров
        [$redirect_url, $notice] = $this->checkParentAccess($ctype, $item, $is_moderator);

        if (is_string($redirect_url)) {
            return ['redirect', $redirect_url, $notice];
        }

        return ['allowed', $notices, null];
    }

    /**
     * Проверяет модерацию и черновики
     *
     * @param array $ctype       Данные типа контента
     * @param array $item        Массив записи
     * @param bool $is_moderator Флаг, что пользователь модератор
     * @return array [bool, ?string]
     */
    public function checkModerationAccess(array $ctype, array $item, bool $is_moderator) {

        if ($item['is_approved']) {
            return [true, null];
        }

        $item_view_notice = $item['is_draft'] ? \LANG_CONTENT_DRAFT_NOTICE : \LANG_MODERATION_NOTICE;

        // Не модератор и не автор
        if (!$is_moderator && $this->cms_user->id != $item['user_id']) {
            return [false, $item_view_notice];
        }

        // Модератор или автор здесь
        // Черновик просто показываем
        if ($item['is_draft']) {
            return [true, $item_view_notice];
        }

        // Запись на модерации
        // ставим флаг, что модератор уже смотрит, после этого изъять из модерации нельзя
        if ($is_moderator) {

            cmsUser::setUPS($this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]), time());

            $item_view_notice = \LANG_MODERATION_NOTICE_MODER;
        }

        $this->viewed_moderators = cmsUser::getSetUPS($this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]));

        if (isset($this->viewed_moderators[$this->cms_user->id])) {
            unset($this->viewed_moderators[$this->cms_user->id]);
        }

        if ($this->viewed_moderators) {

            $viewed_moderators = $this->model_users->
                    filterIn('id', array_keys($this->viewed_moderators))->
                    getUsers();

            $moderator_links = [];

            foreach ($viewed_moderators as $viewed_moderator) {
                $moderator_links[] = '<a href="' . \href_to_profile($viewed_moderator) . '">' . $viewed_moderator['nickname'] . '</a>';
            }

            $count_moderator_links = count($moderator_links);

            $item_view_notice .= sprintf(
                    \LANG_MODERATION_NOTICE_VIEW,
                    ($count_moderator_links > 1 ? \LANG_MODERATORS : \LANG_MODERATOR),
                    implode(', ', $moderator_links),
                    ($count_moderator_links > 1 ? \LANG_MODERATION_VIEWS : \LANG_MODERATION_VIEW),
                    ($count_moderator_links === 1 ? ' ' . mb_strtolower(\string_date_format($this->viewed_moderators[$viewed_moderator['id']], true)) : '')
            );
        }

        return [true, $item_view_notice];
    }

    /**
     * Проверяет ограничения доступа из других контроллеров
     *
     * @param array $ctype       Данные типа контента
     * @param array $item        Массив записи
     * @param bool $is_moderator Флаг, что пользователь модератор
     * @return array [true|string, ?string]
     */
    public function checkParentAccess(array $ctype, array $item, bool $is_moderator) {

        if (empty($item['is_parent_hidden']) && !$item['is_private']) {
            return [true, null];
        }

        $result = cmsEventsManager::hook('content_view_hidden', [
            'viewable'     => true,
            'item'         => $item,
            'is_moderator' => $is_moderator,
            'ctype'        => $ctype
        ]);

        if ($result['viewable']) {
            return [true, null];
        }

        if (isset($result['access_text'])) {
            return [$result['access_redirect_url'] ?? href_to($ctype['name']), $result['access_text']];
        }

        return [$this->getLoginUrl(), null];
    }

    /**
     * Проверяет основные доступы
     *
     * @param array $ctype       Данные типа контента
     * @param array $item        Массив записи
     * @param bool $is_moderator Флаг, что пользователь модератор
     * @return array [?bool, ?string]
     */
    public function checkItemAccess(array $ctype, array $item, bool $is_moderator) {

        $item_view_notice = null;

        $is_owner = $this->cms_user->id == $item['user_id'];

        // Общие права доступа на просмотр
        if (!$is_moderator && !$is_owner && method_exists($this, 'checkListPerm')) {
            if (!$this->checkListPerm($ctype['name'])) {
                return [false, $item_view_notice];
            }
        }

        // Проверяем публикацию
        if ($item['is_pub'] < 1 && !$is_moderator && !$is_owner) {
            return [null, $item_view_notice];
        }

        // Проверяем, что не удалено
        if ($item['is_deleted']) {

            $allow_restore = cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $is_owner);

            if (!$is_moderator && !$allow_restore) {
                return [null, $item_view_notice];
            }

            $item_view_notice = LANG_CONTENT_ITEM_IN_TRASH;
        }

        return [true, $item_view_notice];
    }

}
