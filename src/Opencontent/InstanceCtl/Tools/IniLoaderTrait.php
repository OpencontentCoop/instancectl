<?php

namespace Opencontent\InstanceCtl\Tools;

use Opencontent\InstanceCtl\Ez\eZINI;

trait IniLoaderTrait
{
    /**
     * @param string $fileName
     * @param string|null $siteaccess
     *
     * @return eZINI
     */
    protected function getIni($fileName, $siteaccess = null)
    {
        if (eZINI::getBaseRoot() === null) {
            eZINI::setBaseRoot(getcwd());
        }

        if ($siteaccess) {
            $dirPath = "settings/siteaccess/$siteaccess";
        } else {
            $dirPath = "settings/override";
        }

        $fileName .= '.append.php';

        return new eZINI($fileName, $dirPath, false, false, false, true, false);
    }
}
