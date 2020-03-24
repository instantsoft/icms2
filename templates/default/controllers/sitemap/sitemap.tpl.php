<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url){ ?>
    <url>
        <loc><?php html($url['url']); ?></loc>
<?php if (!empty($url['last_modified']) && $show_lastmod) { ?>
        <lastmod><?php $date_parts = explode(' ', $url['last_modified']); echo $date_parts[0]; ?></lastmod>
<?php } ?>
<?php if ($show_changefreq) { ?>
        <changefreq><?php echo !empty($url['changefreq']) ? $url['changefreq'] : $changefreq; ?></changefreq>
<?php } ?>
<?php if ($show_priority && ($priority || !empty($url['priority']))) { ?>
        <priority><?php echo !empty($url['priority']) ? $url['priority'] : $priority; ?></priority>
<?php } ?>
    </url>
<?php } ?>
</urlset>