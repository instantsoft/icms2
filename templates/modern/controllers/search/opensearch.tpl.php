<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName><?php echo string_short($this->site_config->sitename, 16, '', 'w'); ?></ShortName>
    <Description><?php html(sprintf(LANG_SEARCH_ON, $this->site_config->sitename)); ?></Description>
    <InputEncoding>utf-8</InputEncoding>
    <Image width="210" height="52" type="image/svg+xml"><?php echo $this->site_config->host.'/'.cmsTemplate::TEMPLATE_BASE_PATH.$this->name.'/images/logo.svg'; ?></Image>
    <Url type="text/html" template="<?php echo href_to_abs('search').'?q={searchTerms}'; ?>"/>
</OpenSearchDescription>