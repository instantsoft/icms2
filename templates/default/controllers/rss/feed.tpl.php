<?php
    $config = cmsConfig::getInstance();
    if ($category){ $feed['title'] = $feed['title'].' / '.$category['title']; }
    if ($author){ $feed['title'] = $author['nickname'].' - '.$feed['title']; }
    $feed_title = sprintf(LANG_RSS_FEED_TITLE_FORMAT, $feed['title'], $config->sitename);
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php html($feed_title); ?></title>
        <link><?php html($config->host); ?></link>
        <description><?php html($feed['description']); ?></description>
        <language><?php html(cmsCore::getLanguageName()); ?></language>
        <pubDate><?php html(date('r')); ?></pubDate>
        <?php if (!empty($feed['image'])) { ?>
            <image>
                <url><?php echo $config->upload_host_abs . '/' . $feed['image']['normal']; ?></url>
                <title><?php html($feed_title); ?></title>
                <link><?php html($config->host); ?></link>
            </image>
        <?php } ?>
        <atom:link rel="self" type="application/rss+xml" href="<?php html(href_to_current(true)); ?>"/>
        <?php if (!empty($feed['items'])) { ?>
            <?php foreach ($feed['items'] as $item) { ?>
                <item>
                    <?php if (!empty($feed['mapping']['title'])) { ?>
                        <title><?php echo htmlspecialchars(html_clean($item[$feed['mapping']['title']], 150)); ?></title>
                    <?php } ?>
                    <?php if (!empty($feed['mapping']['description'])) { ?>
                        <description><?php echo htmlspecialchars($item[$feed['mapping']['description']]); ?></description>
                    <?php } ?>
                    <?php if (!empty($feed['mapping']['pubDate'])) { ?>
                        <pubDate><?php html(date('r', strtotime($item[$feed['mapping']['pubDate']]))); ?></pubDate>
                    <?php } ?>
                    <?php if (!empty($feed['mapping']['image'])) { ?>
                        <?php $image = cmsModel::yamlToArray($item[$feed['mapping']['image']]); ?>
                        <?php if (!empty($image[$feed['mapping']['image_size']])) { ?>
                            <?php $imgp = img_get_params($config->upload_path . $image[$feed['mapping']['image_size']]); ?>
                            <enclosure url="<?php echo $config->upload_host_abs . '/' . $image[$feed['mapping']['image_size']]; ?>" type="<?php echo $imgp['mime']; ?>" length="<?php echo $imgp['filesize']; ?>" />
                        <?php } ?>
                    <?php } ?>
                    <link><?php echo $item['page_url']; ?></link>
                    <guid><?php echo $item['page_url']; ?></guid>
                    <?php if (!empty($item['comments_url'])) { ?>
                        <comments><?php echo $item['comments_url']; ?></comments>
                    <?php } ?>
                </item>
            <?php } ?>
        <?php } ?>
    </channel>
</rss>