<?php

    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addCSS('templates/default/css/jquery-ui.css');

    $this->setPageTitle(sprintf(LANG_TAGS_SEARCH_BY_TAG, $tags));

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }

    $this->addBreadcrumb(sprintf(LANG_TAGS_SEARCH_BY_TAG, $tags));

    $content_menu = array();

    if ($is_results){

        foreach($ctypes as $type){
            if ( !in_array($type['name'], array_keys($targets['content'])) ) { continue; }
            $content_menu[] = array(
                'title'    => $type['title'],
                'url'      => $this->href_to('search', array($type['name'])).'?q='.$tags,
                'url_mask' => $this->href_to('search', array($type['name']))
            );
        }

        $content_menu[0]['url']      = $this->href_to('search').'?q='.$tags;
        $content_menu[0]['url_mask'] = $this->href_to('search');

        $this->addMenuItems('results_tabs', $content_menu);

    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => LANG_TAGS_SETTINGS,
            'href'  => href_to('admin', 'controllers', array('edit', 'tags'))
        ));
    }

?>

<h1><?php html(sprintf(LANG_TAGS_SEARCH_BY_TAG, trim($tags, ', '))); ?></h1>

<form action="<?php echo href_to('tags', 'search_tags'); ?>" method="get" id="search_tags_form" class="search_tags_form">
    <?php echo html_input('text', 'q', $tags.', ', array('id' => 'tags')); ?>
    <?php echo html_submit(LANG_FIND); ?>
</form>

<script type="text/javascript">
    var cache = {};

        $( "#tags" ).bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).data( "ui-autocomplete" ).menu.active ) {
            event.preventDefault();
        }
    }).autocomplete({
        minLength: 2,
        source: function( request, response ) {
            var term = request.term;
            if ( term in cache ) {
                response( cache[ term ] );
                return;
            }
            $.getJSON( "<?php echo href_to('tags', 'autocomplete'); ?>", {
                term: term.split( /,\s*/ ).pop()
            }, function( data, status, xhr ) {
                cache[ term ] = data;
                response( data );
            });
        },
        focus: function() {
            return false;
        },
        select: function( event, ui ) {
            var terms = this.value.split( /,\s*/ );
            terms.pop();
            terms.push( ui.item.value );
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
        }
    });
</script>

<?php if (!$is_results){ ?>
    <p><?php echo LANG_TAGS_SEARCH_NO_RESULTS; ?></p>
<?php } ?>

<?php if ($is_results){ ?>

    <div id="tags_search_pills">
        <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
    </div>

    <div id="tags_search_list"><?php echo $html; ?></div>

<?php } ?>
