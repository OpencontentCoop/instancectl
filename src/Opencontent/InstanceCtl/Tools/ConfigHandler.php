<?php

namespace Opencontent\InstanceCtl\Tools;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\VarDumper\VarDumper;

class ConfigHandler
{
    const CONFIG_FILENAME = 'instancectl.config';

    const GATEWAY_FILESYSTEM = 'filesystem';

    const GATEWAY_REMOTE = 'remote';

    private $currentDirectory;

    private $currentFilepath;

    public function __construct()
    {
        $this->setCurrentDirectory(getcwd());
    }

    /**
     * @return string
     */
    public function getCurrentDirectory()
    {
        return $this->currentDirectory;
    }

    /**
     * @param string $currentDirectory
     */
    public function setCurrentDirectory($currentDirectory)
    {
        $this->currentDirectory = $currentDirectory;
        $this->currentFilepath = $this->currentDirectory . '/' . self::CONFIG_FILENAME;
    }

    /**
     * @return mixed
     */
    public function getCurrentFilepath()
    {
        return $this->currentFilepath;
    }

    public function exists()
    {
        return file_exists($this->currentFilepath);
    }

    public function create(array $data = array())
    {
        $config = new Config($data);
        $yaml = Yaml::dump($config->toArray());
        file_put_contents($this->currentFilepath, $yaml);
    }

    public function store(Config $config)
    {
        $yaml = Yaml::dump($config->toArray());
        file_put_contents($this->currentFilepath, $yaml);
    }

    public function dump()
    {
        $data = null;
        if (file_exists($this->currentFilepath)) {
            $data = Yaml::parse(file_get_contents($this->currentFilepath));
        }
        VarDumper::dump($data);
    }

    /**
     * @return Config
     */
    public static function current()
    {
        $data = null;
        $handler = new ConfigHandler();
        if (file_exists($handler->currentFilepath)) {
            $data = Yaml::parse(file_get_contents($handler->currentFilepath));
        }

        return new Config($data);
    }
}
