<?php

namespace Opencontent\InstanceCtl\Tools\Gateway;

use Opencontent\InstanceCtl\Tools\ConfigHandler;
use Opencontent\InstanceCtl\Tools\Instance;
use Opencontent\InstanceCtl\Tools\InstanceExporter;
use Symfony\Component\Yaml\Yaml;

class Filesystem extends AbstractGateway
{
    /**
     * @param Instance[] $instances
     */
    public function store(array $instances)
    {
        $filename = $this->getFilename() ? $this->getFilename() : ConfigHandler::current()->instances_filename;
        $filepath = getcwd() . '/' . $filename;

        $data = array(
            'server' => ConfigHandler::current()->server_name,
            'document_root' => ConfigHandler::current()->document_root,
            'generator' => 'instancectl generate',
            'instances' => array(),
            'timestamp' => time()
        );
        ksort($instances);
        foreach($instances as $instance){
            $data['instances'][$instance->getIdentifier()] = $this->getExporter()->toYamlArray($instance);
        }
        $yaml = Yaml::dump($data, 10);
        $this->getOutput()->writeln("Store data in $filepath");
        file_put_contents($filepath, $yaml);
    }

    public function read()
    {
        $filename = $this->getFilename() ? $this->getFilename() : ConfigHandler::current()->instances_filename;
        $data = file_get_contents($filename);
        $yaml = Yaml::parse($data);

        return $yaml;
    }
}
