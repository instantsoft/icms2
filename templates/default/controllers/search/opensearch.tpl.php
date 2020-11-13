<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName><?php html(sprintf(LANG_SEARCH_ON, $site_config->sitename)); ?></ShortName>
    <Description><?php html(sprintf(LANG_SEARCH_ON, $site_config->sitename)); ?></Description>
    <InputEncoding>utf-8</InputEncoding>
    <Image width="166" height="23" type="image/png"><?php echo $site_config->host.'/'.cmsTemplate::TEMPLATE_BASE_PATH.$this->name.'/images/logo.png'; ?></Image>
    <Url type="text/html" template="<?php echo href_to_abs('search').'?q={searchTerms}'; ?>"/>
</OpenSearchDescription>