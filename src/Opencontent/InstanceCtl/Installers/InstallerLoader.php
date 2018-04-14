<?php

namespace Opencontent\InstanceCtl\Installers;


class InstallerLoader
{
    /**
     * @param $component
     *
     * @return InstallerInterface
     * @throws \Exception
     */
    public static function load($component)
    {
        if ($component == 'booking'){
            return new Booking();
        }

        throw new \Exception("Installer $component not found");
    }
}
