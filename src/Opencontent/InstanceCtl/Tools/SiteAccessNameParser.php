<?php

namespace Opencontent\InstanceCtl\Tools;


class SiteAccessNameParser
{
    public static function getInstanceIdentifier($siteAccessName)
    {
        $parts = explode('_', $siteAccessName);

        return array_shift($parts);
    }
}
