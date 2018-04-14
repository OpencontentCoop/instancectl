<?php

namespace Opencontent\InstanceCtl\Tools;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class LegacyKernel
{
    protected $previousRunningDir;

    protected $legacyRootDir;

    protected $siteaccessName;

    public function __construct($legacyRootDir, $siteaccessName = null)
    {
        $this->legacyRootDir = $legacyRootDir;
        $this->siteaccessName = $siteaccessName;
    }

    public function run(\Closure $callback)
    {
        $this->previousRunningDir = getcwd();
        chdir($this->legacyRootDir);

        $fs = new Filesystem();
        if (!$fs->exists('autoload.php')) {
            throw new FileNotFoundException("Legacy root dir not found");
        }

        require 'autoload.php';

        $settings = array();
        if ($this->siteaccessName) {

            if (!$fs->exists("settings/siteaccess/{$this->siteaccessName}")) {
                throw new FileNotFoundException("Siteaccess {$this->siteaccessName} not found");
            }

            $access = array(
                'name' => $this->siteaccessName,
                'type' => \eZSiteAccess::TYPE_STATIC,
                'uri_part' => array()
            );
            $settings = array('siteaccess' => $access);
        }
        $kernel = new \ezpKernel(new \ezpKernelWeb($settings));

        if ($this->siteaccessName) {
            \eZSiteAccess::change($access);
        }

        $return = $kernel->runCallback($callback, true);

        $previousDir = $this->previousRunningDir;
        $this->previousRunningDir = null;
        chdir($previousDir);

        return $return;
    }

    public static function call($callback, $legacyRootDir, $siteaccessName = null)
    {
        $kernel = new LegacyKernel($legacyRootDir, $siteaccessName);
        $return = $kernel->run($callback);

        return $return;
    }
}
