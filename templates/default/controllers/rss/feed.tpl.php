<?php
    if ($category){ $feed['title'] = $feed['title'] . ' / ' . $category['title']; }
    if ($author){ $feed['title'] = $author['nickname'] . ' - ' . $feed['title']; }
    $feed_title = sprintf(LANG_RSS_FEED_TITLE_FORMAT, $feed['title'], cmsConfig::get('sitename'));
    $feed_url = cmsConfig::get('host');
?>
<?php echo '<?xml version="1.0"?>' . "\n"; ?>
<rss version="2.0">
    <channel>
        <title><?php html($feed_title); ?></title>
        <link><?php html($feed_url); ?></link>
        <description><?php html($feed['description']); ?></description>
        <language><?php html(cmsConfig::get('language')); ?></language>
        <pubDate><?php html(date('r')); ?></pubDate>

        <?php if(!empty($feed['image'])) { ?>
            <image>
                <url><?php echo cmsConfig::get('upload_host') . '/' . $feed['image']['normal']; ?></url>
                <title><?php html($feed_title); ?></title>
                <link><?php html($feed_url); ?></link>
            </image>
        <?php } ?>

        <?php if(!empty($feed['items'])) { ?>
            <?php foreach($feed['items'] as $item){ ?>
                    <item>
                        <?php if(!empty($feed['mapping']['title'])) { ?>
                            <title><?php html($item[$feed['mapping']['title']]); ?></title>
                        <?php } ?>
                        <?php if(!empty($feed['mapping']['description'])) { ?>
                            <description><?php html($item[$feed['mapping']['description']]); ?></description>
                        <?php } ?>
                        <?php if(!empty($feed['mapping']['pubDate'])) { ?>
                            <pubDate><?php html(date('r', strtotime($item[$feed['mapping']['pubDate']]))); ?></pubDate>
                        <?php } ?>
                        <?php if(!empty($feed['mapping']['image'])) { ?>
                            <?php $image = cmsModel::yamlToArray($item[$feed['mapping']['image']]); ?>
							<?php if (!empty($image[$feed['mapping']['image_size']])){ ?>
								<enclosure url="<?php echo cmsConfig::get('upload_host_abs') . '/' . $image[$feed['mapping']['image_size']]; ?>" />
							<?php } ?>
                        <?php } ?>
                        <link><?php echo href_to_abs($feed['ctype_name'], $item['slug'].'.html'); ?></link>
                        <guid><?php echo href_to_abs($feed['ctype_name'], $item['slug'].'.html'); ?></guid>
                    </item>
            <?php } ?>
        <?php } ?>

   </channel>
</rss>
