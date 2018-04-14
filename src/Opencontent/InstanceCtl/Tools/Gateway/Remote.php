<?php

namespace Opencontent\InstanceCtl\Tools\Gateway;

use Opencontent\InstanceCtl\Tools\ConfigHandler;
use Opencontent\InstanceCtl\Tools\Instance;
use Symfony\Component\VarDumper\VarDumper;

class Remote extends AbstractGateway
{
    /**
     * @param Instance[] $instances
     *
     * @throws \Exception
     */
    public function store(array $instances)
    {
        if (ConfigHandler::current()->instancectl_server_type == 'ez'){
            foreach($instances as $instance){
                print_r($this->getExporter()->toRemotePayloadArray($instance));
            }

        }else{
            $this->getOutput()->writeln("Non ancora implememtato " . __FILE__);
        }
    }
}
