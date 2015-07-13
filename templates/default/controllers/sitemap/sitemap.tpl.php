<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url=>$date_last_modified){ ?>
   <url>
      <loc><?php html($url); ?></loc>
<?php if ($date_last_modified) { ?>
      <lastmod><?php $date_parts = explode(' ', $date_last_modified); echo $date_parts[0]; ?></lastmod>
<?php } ?>
   </url>
<?php } ?>
</urlset>