<?php

class modelTags extends cmsModel {

    const TAG_LEN = 32;

    public function filterTarget($controller, $subject, $id) {

        $this->filterEqual('target_controller', $controller);
        $this->filterEqual('target_subject', $subject);
        $this->filterEqual('target_id', $id);

        return $this;
    }

    public function addTags($tags_string, $controller, $subject, $id) {

        if (!$tags_string) { return null; }

        $tags_string = trim($tags_string);
        if (!$tags_string) { return null; }

        $tags = explode(',', $tags_string);

        $tags_ids      = [];
        $tags_inserted = [];

        foreach ($tags as $tag) {

            $tag = mb_strtolower(trim($tag));

            if (!$tag) { continue; }

            if (mb_strlen($tag) > self::TAG_LEN) { continue; }

            if (in_array($tag, $tags_inserted)) {
                continue;
            }

            $tag_id = $this->registerTag($tag);
            if (!$tag_id) { continue; }

            $this->insert('tags_bind', [
                'tag_id'            => $tag_id,
                'target_controller' => $controller,
                'target_subject'    => $subject,
                'target_id'         => $id
            ]);

            $tags_inserted[] = $tag;
            $tags_ids[]      = $tag_id;
        }

        $this->recountTagsFrequency($tags_ids);

        cmsCache::getInstance()->clean('tags.tags');

        return implode(', ', $tags_inserted);
    }

    public function recountTagsFrequency($tags_ids = []) {

        $this->select('t.id', 'tag_id')->
            select('COUNT(i.tag_id)', 'frequency')->
            joinRight('tags', 't', 't.id = i.tag_id')->
            groupBy('t.id');

        if ($tags_ids) {
            $this->filterIn('t.id', $tags_ids);
        }

        $binds = $this->get('tags_bind');

        if (is_array($binds)) {
            foreach ($binds as $item) {
                if ($item['frequency']) {
                    $this->update('tags', $item['tag_id'], ['frequency' => $item['frequency']]);
                } else {
                    $this->deleteTag($item['tag_id']);
                }
            }
        }

        cmsCache::getInstance()->clean('tags.tags');

        return true;
    }

    public function updateTags($tags_string, $controller, $subject, $id) {

        $this->filterTarget($controller, $subject, $id);

        $this->lockFilters();

        $tags_ids = $this->get('tags_bind', function ($item, $model) {
            return $item['tag_id'];
        });

        $this->unlockFilters();

        $this->deleteFiltered('tags_bind');

        if ($tags_ids) {
            $this->recountTagsFrequency($tags_ids);
        }

        return $this->addTags($tags_string, $controller, $subject, $id);
    }

    public function registerTag($tag) {

        $id = $this->insertOrUpdate('tags', ['tag' => $tag]);

        if (!$id) {
            return $this->getTagId($tag);
        }

        cmsCache::getInstance()->clean('tags.tags');

        return $id;
    }

    public function getTagId($tag) {
        return $this->filterEqual('tag', $tag)->getFieldFiltered('tags', 'id');
    }

    public function getTagByTag($tag) {
        return $this->filterEqual('tag', $tag)->getItem('tags');
    }

    public function getTagTargets($tag_id) {

        $binds = $this->filterEqual('tag_id', $tag_id)->get('tags_bind');

        if (!$binds) {
            return false;
        }

        $targets = [];

        foreach ($binds as $bind) {
            $targets[$bind['target_controller']][] = $bind['target_subject'];
        }

        return $targets;
    }

    public function getTagsForTarget($controller, $subject, $id) {

        $this->useCache('tags.tags');

        $this->filterTarget($controller, $subject, $id);

        $this->select('t.tag', 'tag');

        $this->join('tags', 't', 't.id = i.tag_id');

        return $this->get('tags_bind', function ($item, $model) {
            return $item['tag'];
        }) ?: [];
    }

    public function getTagsIDsForTarget($controller, $subject, $id) {

        $this->useCache('tags.tags');

        $this->filterTarget($controller, $subject, $id);

        $this->select('t.tag', 'tag');

        $this->join('tags', 't', 't.id = i.tag_id');

        return $this->get('tags_bind', function ($item, $model) {
            return $item['tag_id'];
        }) ?: [];
    }

    public function getTagsStringForTarget($controller, $subject, $id) {

        $tags = $this->getTagsForTarget($controller, $subject, $id);

        if ($tags) {
            return implode(', ', $tags);
        }

        return '';
    }

    public function getTagsCount() {
        return $this->getCount('tags');
    }

    public function getTags() {

        $this->useCache('tags.tags');

        return $this->get('tags');
    }

    public function getTag($tag_id) {

        $this->useCache('tags.tags');

        return $this->getItemById('tags', $tag_id);
    }

    public function getMaxTagFrequency() {

        $this->useCache('tags.tags');

        return $this->getMax('tags', 'frequency');
    }

    public function updateTag($tag_id, $tag) {

        cmsCache::getInstance()->clean('tags.tags');

        return $this->update('tags', $tag_id, $tag);
    }

    public function replaceTargetTags($tag_id, $new_tag, $old_tag) {

        $binds = $this->filterEqual('tag_id', $tag_id)->get('tags_bind');
        if (!$binds) {
            return false;
        }

        $targets = [];

        foreach ($binds as $bind) {
            $targets[$bind['target_controller']][$bind['target_subject']][] = $bind['target_id'];
        }

        foreach ($targets as $target_controller => $target) {

            $model = cmsCore::getModel($target_controller);

            if (!method_exists($model, 'replaceCachedTags')) {
                continue;
            }

            foreach ($target as $target_subject => $target_ids) {

                $target_ids = array_unique($target_ids);

                $model->replaceCachedTags($target_subject, $target_ids, $new_tag, $old_tag);
            }
        }
    }

    public function deleteTag($tag_id) {

        $this->delete('tags', $tag_id);

        $this->filterEqual('tag_id', $tag_id)->deleteFiltered('tags_bind');

        cmsCache::getInstance()->clean('tags.tags');
    }

    public function deleteTags($controller, $subject, $id) {

        $tags_ids = $this->filterTarget($controller, $subject, $id)->
                get('tags_bind', function ($item, $model) {
            return $item['tag_id'];
        });

        if (!$tags_ids) { return false; }

        $this->filterIn('id', array_keys($tags_ids))->deleteFiltered('tags_bind');

        return $this->recountTagsFrequency(array_unique($tags_ids));
    }

    public function mergeTags($child_id, $parent_id) {

        $this->filterEqual('tag_id', $child_id)->
            updateFiltered('tags_bind', [
                'tag_id' => $parent_id
        ]);

        $this->recountTagsFrequency([$parent_id]);

        $this->deleteTag($child_id);

        return $this->removeDoubles($parent_id);
    }

    public function removeDoubles($tag_id) {

        $this->select('COUNT(i.tag_id) as qty')->
            filterEqual('tag_id', $tag_id)->
            groupBy('target_controller, target_subject, target_id')->
            get('tags_bind', function ($item, $model) {
                if ($item['qty'] > 1) {
                    $model->delete('tags_bind', $item['id']);
                }
            });

        cmsCache::getInstance()->clean('tags.tags');

        return true;
    }

}
