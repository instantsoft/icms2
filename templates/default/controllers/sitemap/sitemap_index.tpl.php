<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($sitemaps as $file){ ?>
   <sitemap>
      <loc><?php html($host . "/{$file}"); ?></loc>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
   </sitemap>
<?php } ?>
</sitemapindex>