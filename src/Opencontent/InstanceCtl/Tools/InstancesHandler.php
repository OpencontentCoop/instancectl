<?php

namespace Opencontent\InstanceCtl\Tools;

use Opencontent\InstanceCtl\Tools\Gateway;
use Symfony\Component\VarDumper\VarDumper;

class InstancesHandler
{
    public static function dump(Instance $instance)
    {
        VarDumper::dump($instance);
    }

    /**
     * @param Instance[] $instances
     */
    public static function dumpList(array $instances)
    {
        foreach ($instances as $instance) {
            self::dump($instance);
        }
    }

    /**
     * @param Instance[] $instances
     */
    public static function setCache(array $instances)
    {
        $cacheFilepath = getcwd() . '/' . ConfigHandler::current()->cache_filename;
        $data = serialize($instances);
        file_put_contents($cacheFilepath, $data);
    }

    public static function hasCache()
    {
        $cacheFilepath = getcwd() . '/' . ConfigHandler::current()->cache_filename;

        return file_exists($cacheFilepath);
    }

    public static function getCache()
    {
        $cacheFilepath = getcwd() . '/' . ConfigHandler::current()->cache_filename;
        $data = file_get_contents($cacheFilepath);

        return unserialize($data);
    }

    /**
     * @param array $instances
     * @param null $filename
     *
     * @return Gateway\AbstractGateway
     * @throws \Exception
     */
    public static function getGateway(array $instances)
    {
        $gateway = ConfigHandler::current()->instancectl_gateway;

        if ($gateway == ConfigHandler::GATEWAY_FILESYSTEM) {
            $gateway = new Gateway\Filesystem();

        } elseif ($gateway == ConfigHandler::GATEWAY_REMOTE) {
            $gateway = new Gateway\Remote();

        } else {
            throw new \Exception("Gateway $gateway not found");
        }

        return $gateway;

    }

    /**
     * @param $identifier
     *
     * @return Instance|Instance[]
     * @throws \Exception
     */
    public static function load($identifier = null)
    {
        $instances = self::getCache();
        if ($identifier) {
            if (isset( $instances[$identifier] )) {
                return $instances[$identifier];
            }

            throw new \Exception("Instance $identifier not found. Try to regenerate instances cache");
        }

        return $instances;
    }

    public static function filter($filter = null)
    {
        $instances = self::getCache();
        if ($filter){

        }

        return $instances;
    }
}
