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

    /**
     * @return Instance[]
     */
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
    public static function getGateway()
    {
        $gateway = new Gateway\Filesystem();

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
            if (isset($instances[$identifier])) {
                return $instances[$identifier];
            }

            throw new \Exception("Instance $identifier not found. Try to regenerate instances cache");
        }

        return $instances;
    }

    public static function filter($filter = null)
    {
        $_instances = self::getCache();
        if ($filter) {
            $instances = array();
            foreach ($_instances as $instance){
                if (in_array($filter, $instance->getTags())){
                    $instances[$instance->getIdentifier()] = $instance;
                }
                ksort($instances);
            }
        }else{
            $instances = $_instances;
        }

        return $instances;
    }

    public static function getStatus()
    {
        $gateway = self::getGateway();
        $data = $gateway->read();

        return array(
            'instances_count' => count($data['instances']),
            'last_update' => (new \DateTime())->setTimestamp($data['timestamp'])->format(DATE_RSS)
        );
    }
}
