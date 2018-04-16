<?php

namespace Opencontent\InstanceCtl\Tools\SiteAccessIdentifierParser;

use Opencontent\InstanceCtl\Tools\SiteAccessIdentifierParserInterface;

class SiteAccessIdentifierUnderscoreParser implements SiteAccessIdentifierParserInterface
{
    public function parse($siteAccessName)
    {
        $parts = explode('_', $siteAccessName);

        return array_shift($parts);
    }
}
