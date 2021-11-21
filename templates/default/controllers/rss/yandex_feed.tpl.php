<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
    <channel>
        <title><?php html($feed_title); ?></title>
        <link><?php html($this->site_config->host); ?></link>
        <description><?php html($feed['description']); ?></description>
        <?php if(!empty($feed['image'])) { ?>
            <image>
                <url><?php echo $this->site_config->upload_host_abs.'/'.$feed['image']['normal']; ?></url>
                <title><?php html($feed_title); ?></title>
                <link><?php html($this->site_config->host); ?></link>
            </image>
            <yandex:logo><?php echo $this->site_config->upload_host_abs.'/'.$feed['image']['normal']; ?></yandex:logo>
            <yandex:logo type="square"><?php echo $this->site_config->upload_host_abs.'/'.$feed['image']['normal']; ?></yandex:logo>
        <?php } ?>
        <atom:link rel="self" type="application/rss+xml" href="<?php html(href_to_current(true)); ?>"/>
        <?php if(!empty($feed['items'])) { ?>
            <?php foreach($feed['items'] as $item){ ?>
                <item>
                    <?php if(!empty($feed['mapping']['title'])) { ?>
                        <title><?php echo htmlspecialchars($item[$feed['mapping']['title']]); ?></title>
                    <?php } ?>
                    <link><?php echo $item['page_url']; ?></link>
                    <?php if(!empty($feed['mapping']['description'])) { ?>
                        <description><?php echo htmlspecialchars(html_clean($item[$feed['mapping']['description']], 150)); ?></description>
                        <yandex:full-text><?php echo htmlspecialchars(html_clean($item[$feed['mapping']['description']])); ?></yandex:full-text>
                    <?php } ?>
                    <?php if(!empty($feed['mapping']['image'])) { ?>
                        <?php $image = cmsModel::yamlToArray($item[$feed['mapping']['image']]); ?>
                        <?php if (!empty($image[$feed['mapping']['image_size']])){ ?>
                            <?php $imgp = img_get_params($this->site_config->upload_path.$image[$feed['mapping']['image_size']]); ?>
                            <enclosure url="<?php echo $this->site_config->upload_host_abs.'/'.$image[$feed['mapping']['image_size']]; ?>" type="<?php echo $imgp['mime']; ?>" length="<?php echo $imgp['filesize']; ?>" />
                        <?php } ?>
                    <?php } ?>
                    <?php if(!empty($item['user']['nickname'])) { ?>
                        <dc:creator><?php html($item['user']['nickname']); ?></dc:creator>
                    <?php } ?>
                    <?php if(!empty($feed['mapping']['pubDate'])) { ?>
                        <pubDate><?php html(date('r', strtotime($item[$feed['mapping']['pubDate']]))); ?></pubDate>
                    <?php } ?>
                </item>
            <?php } ?>
        <?php } ?>
    </channel>
</rss>