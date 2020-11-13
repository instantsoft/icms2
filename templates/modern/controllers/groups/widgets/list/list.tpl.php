<?php
$this->addTplCSSFromContext('controllers/groups/styles');
?>
<div class="groups-list content_list striped-list list-64">
    <?php foreach($groups as $group){ ?>
        <div class="item media mb-3 align-items-center">
            <?php if (in_array($fields['logo']['id'], $fields_is_in_list) && $group['logo']){ ?>
                <a class="icms-user-avatar d-flex mr-3" href="<?php echo href_to('groups', $group['slug']); ?>">
                    <?php echo html_image($group['logo'], $fields['logo']['handler']->getOption('size_teaser'), $group['title']); ?>
                </a>
            <?php } ?>
            <div class="media-body <?php if (!empty($group['fields'])) { ?>fields_available<?php } ?>">
                <?php if (in_array($fields['title']['id'], $fields_is_in_list)){ ?>
                    <h5 class="my-0">
                        <a href="<?php echo href_to('groups', $group['slug']); ?>">
                            <?php html($group['title']); ?>
                        </a>
                        <?php if ($group['is_closed']) { ?>
                            <span class="is_closed text-muted ml-2" title="<?php html(LANG_GROUP_IS_CLOSED_ICON); ?>" data-toggle="tooltip" data-placement="top">
                                <?php html_svg_icon('solid', 'lock'); ?>
                            </span>
                        <?php } ?>
                    </h5>
                <?php } ?>
                <?php if (!empty($group['fields'])) { ?>
                    <div class="fields mt-2">
                        <?php foreach($group['fields'] as $field){ ?>
                            <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                                <?php if ($field['label_pos'] != 'none'){ ?>
                                    <div class="title_<?php echo $field['label_pos']; ?>">
                                        <?php echo $field['title'] . ($field['label_pos'] == 'left' ? ': ' : ''); ?>
                                    </div>
                                <?php } ?>
                                <div class="value">
                                    <?php echo $field['html']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($show_members_count) { ?>
                <div class="actions text-muted ml-2">
                    <?php echo $group['members_count'] ? html_spellcount($group['members_count'], LANG_GROUPS_MEMBERS_SPELLCOUNT) : '&mdash;'; ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>