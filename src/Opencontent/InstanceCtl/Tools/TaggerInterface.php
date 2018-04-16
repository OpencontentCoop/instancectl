<?php

namespace Opencontent\InstanceCtl\Tools;


interface TaggerInterface
{
    /**
     * @param Instance $instance
     *
     * @return string[]
     */
    public function tag(Instance $instance);
}
